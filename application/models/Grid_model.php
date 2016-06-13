<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Grid_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }    
    
    public function set_start_date($v)
    {
        $_SESSION['grid_start_date'] = $v;
    }
    
    public function get_start_date()
    {
        return isset($_SESSION['grid_start_date']) ? $_SESSION['grid_start_date'] : date('Y-m-d', strtotime("-7 days"));
    }
    
    public function set_end_date($v)
    {
        $_SESSION['grid_end_date'] = $v;
    }
    
    public function get_end_date()
    {
        return isset($_SESSION['grid_end_date']) ? $_SESSION['grid_end_date'] : date('Y-m-d', strtotime("-1 days"));
    }
    
    public function get() {
        $sql = "
with 
data_ua as
(select af_cost_currency,af_cost_model,af_cost_value,af_siteid,media_source,campaign,
date_joined,last_active,datediff(days,date_joined,last_active) as lifetime,milliseconds_played,spend,
user_businesstier,user_crystaluse,
date_joined::date as dates,
swrve_valid_iap,swrve_invalid_iap,swrve_session_start 
from appsflyer_ios_in_app_event_non_organic a
LEFT JOIN swrve_properties_ios sp ON a.customer_user_id=sp.swrve_user_id 
LEFT JOIN swrve_events_fired_ios se ON se.swrve_data_id=sp.swrve_data_id 
LEFT JOIN custom_properties_ios cp ON cp.swrve_data_id=sp.swrve_data_id 
where a.event_type='install'
and date_joined::date>= '".$this->get_start_date()."' -- Tanggal Start
and date_joined::date<='".$this->get_end_date()."' -- Tanggal end
),


data_ua_date as(
select dates,media_source as referrer_name,af_siteid as sub_campaign, campaign as campaign_name
,spend::REAL/100 as revenue
,decode(af_cost_value, 'null', 0, af_cost_value::REAL) as cost
,case when spend>0 then 1 else 0 end as spending_user
,swrve_session_start as session
,milliseconds_played/60000 as session_length
,lifetime as lifetime
,case when lifetime>=1 then 1 else 0 end as retention_D1
,case when lifetime>=3 then 1 else 0 end as retention_D3
,case when lifetime>=7 then 1 else 0 end as retention_D7
,user_crystaluse as crystaluse
,user_businesstier as businesstier
,median(user_crystaluse) over (partition by referrer_name) as med_crystaluse
,median(user_businesstier) over (partition by referrer_name) as med_businesstier
from data_ua
),


data_modus_campaign_name 
as (
select referrer_name,campaign_name,concat(concat(businesstier,' ('),
              concat(count_user,case when count_user=1 then ' user)' else ' users)' end )) as mod_tier
from 
(select referrer_name,campaign_name,businesstier,count_user,
row_number() OVER (PARTITION BY referrer_name,campaign_name ORDER BY count_user DESC,businesstier DESC) AS rank_count
from (
select referrer_name,campaign_name,businesstier,count(1) as count_user
from data_ua_date
group by referrer_name,campaign_name,businesstier
) as data_count_tier
) ranks
where rank_count=1
),

data_modus_referrer_name 
as (
select referrer_name,referrer_name as campaign_name,concat(concat(businesstier,' ('),
              concat(count_user,case when count_user=1 then ' user)' else ' users)' end )) as mod_tier
from 
(select referrer_name,businesstier,count_user,
row_number() OVER (PARTITION BY referrer_name ORDER BY count_user DESC,businesstier DESC) AS rank_count
from (
select referrer_name,businesstier,count(1) as count_user
from data_ua_date
group by referrer_name,businesstier
) as data_count_tier
) ranks
where rank_count=1
)

select referrer_name,referrer_name as campaign_name, 0 as node
,round(sum(revenue),2) as total_revenue
 ,round(sum(cost),2) as spend
 ,round(sum(cost)/count(1),2) as cpi
,count(1) as install
 ,round(sum(revenue)/count(1),2) as arpu
 ,round(sum(revenue)/nullif(sum(spending_user),0),2) as arppu
,100*sum(spending_user)/count(1) as ppu
--,100*(sum(revenue)-sum(cost))/nullif(sum(cost),0) as roi
,round((sum(revenue)-sum(cost)),2) as roi
,sum(session)/count(1) as average_session
,sum(session_length)/count(1) as average_session_length
,sum(lifetime)/count(1) as average_lifetime
,100*sum(retention_D1)/count(1) as D1_retention
,100*sum(retention_D3)/count(1) as D3_retention
,100*sum(retention_D7)/count(1) as D7_retention
,min(mod_tier) as modus_businesstier
,round(min(med_businesstier),0) as median_businesstier
,round(sum(crystaluse)/count(1),0) as mean_crystaluse
,round(min(med_crystaluse),0) as median_crystaluse
,coalesce(min(arpu_limit),-1) as arpu_limit
,coalesce(min(cpi_limit),-1) as cpi_limit
,coalesce(min(ppu_limit),-1) as ppu_limit
,coalesce(min(d1_limit),-1) as d1_limit
,coalesce(min(d3_limit),-1) as d3_limit
,coalesce(min(d7_limit),-1) as d7_limit

from data_ua_date 
left join data_modus_referrer_name using (referrer_name)
left join tbl_ua_setting on data_ua_date.referrer_name = tbl_ua_setting.channel
group by referrer_name

UNION ALL

select referrer_name,campaign_name, 1 as node
,round(sum(revenue),2) as total_revenue
 ,round(sum(cost),2) as spend
 ,round(sum(cost)/count(1),2) as cpi
,count(1) as install
 ,round(sum(revenue)/count(1),2) as arpu
 ,round(sum(revenue)/nullif(sum(spending_user),0),2) as arppu
,100*sum(spending_user)/count(1) as ppu
--,100*(sum(revenue)-sum(cost))/nullif(sum(cost),0) as roi
,round(sum(revenue)-sum(cost),2) as roi
,sum(session)/count(1) as average_session
,sum(session_length)/count(1) as average_session_length
,sum(lifetime)/count(1) as average_lifetime
,100*sum(retention_D1)/count(1) as D1_retention
,100*sum(retention_D3)/count(1) as D3_retention
,100*sum(retention_D7)/count(1) as D7_retention
,min(mod_tier) as modus_businesstier
,round(min(med_businesstier),0) as median_businesstier
,round(sum(crystaluse)/count(1),0) as mean_crystaluse
,round(min(med_crystaluse),0) as median_crystaluse
,-1 as arpu_limit
,-1 as cpi_limit
,-1 as ppu_limit
,-1 as d1_limit
,-1 as d3_limit
,-1 as d7_limit

from data_ua_date 
left join data_modus_campaign_name using (referrer_name,campaign_name)
group by referrer_name,campaign_name
order by referrer_name, node, campaign_name

;
";
//        echo $sql; die;
        $query = $this->db->query($sql);        
        return $query->result_array();
    }

}
