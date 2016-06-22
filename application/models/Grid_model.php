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
data_ua_date as(
select *
,case when lifetime>=1 then 1 else 0 end as retention_D1
,case when lifetime>=3 then 1 else 0 end as retention_D3
,case when lifetime>=7 then 1 else 0 end as retention_D7
,median(crystaluse) over (partition by referrer_name,campaign_name) as med_crystaluse
,median(businesstier) over (partition by referrer_name,campaign_name) as med_businesstier
,median(crystaluse) over (partition by referrer_name) as med_crystaluse_ref
,median(businesstier) over (partition by referrer_name) as med_businesstier_ref
from data_ua
where dates between '".$this->get_start_date()."'  and '".$this->get_end_date()."'  -- Tanggal  Start and end
),


data_modus_campaign_name 
as (
select referrer_name,campaign_name,businesstier as mod_tier
from 
(
select referrer_name,campaign_name,businesstier,count(1) as count_user
,row_number() OVER (PARTITION BY referrer_name,campaign_name ORDER BY count(1) DESC,businesstier DESC) AS rank_count
from data_ua_date
group by referrer_name,campaign_name,businesstier
) as data_count_tier
where rank_count=1
),

data_modus_referrer_name 
as (
select referrer_name,referrer_name as campaign_name,businesstier as mod_tier
from 
( select referrer_name,businesstier,count(1) as count_user
,row_number() OVER (PARTITION BY referrer_name ORDER BY count(1) DESC,businesstier DESC) AS rank_count
from data_ua_date
group by referrer_name,businesstier
) as data_count_tier
where rank_count=1
)

select referrer_name,referrer_name as campaign_name, 0 as node
,round(sum(revenue),2) as total_revenue
 ,round(sum(costs),2) as spend
 ,round(sum(costs)/count(1),2) as cpi
,count(1) as install
 ,round(sum(revenue)/count(1),2) as arpu
 ,round(sum(revenue)/nullif(sum(spending_user),0),2) as arppu
,100*sum(spending_user)/count(1) as ppu
,round((sum(revenue)-sum(costs)),2) as roi
,nvl(100*round((sum(revenue)-sum(costs))/nullif(sum(costs),0),2),0) as roi_percent
,nvl(100*round(sum(revenue)/nullif(sum(costs),0),2),0) as roas_percent
,sum(session)/count(1) as average_session
,sum(session_length)/count(1) as average_session_length
,sum(lifetime)/count(1) as average_lifetime
,100*sum(retention_D1)/count(1) as D1_retention
,100*sum(retention_D3)/count(1) as D3_retention
,100*sum(retention_D7)/count(1) as D7_retention
,min(mod_tier) as modus_businesstier
,round(min(med_businesstier_ref),0) as median_businesstier
,round(sum(crystaluse)/count(1),0) as mean_crystaluse
,round(min(med_crystaluse_ref),0) as median_crystaluse
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
 ,round(sum(costs),2) as spend
 ,round(sum(costs)/count(1),2) as cpi
,count(1) as install
 ,round(sum(revenue)/count(1),2) as arpu
 ,round(sum(revenue)/nullif(sum(spending_user),0),2) as arppu
,100*sum(spending_user)/count(1) as ppu
,round(sum(revenue)-sum(costs),2) as roi
,nvl(100*round((sum(revenue)-sum(costs))/nullif(sum(costs),0),2),0) as roi_percent
,nvl(100*round(sum(revenue)/nullif(sum(costs),0),2),0) as roas_percent
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
