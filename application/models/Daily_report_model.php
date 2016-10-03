<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Daily_report_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }    
    
    public function set_country($v)
    {
        $_SESSION['daily_report_country'] = $v;
    }
    
    public function get_country()
    {
        return isset($_SESSION['daily_report_country']) ? $_SESSION['daily_report_country'] : 'US';
    }
    
    public function set_start_date($v)
    {
        $_SESSION['daily_report_start_date'] = $v;
    }
    
    public function get_start_date()
    {
        return isset($_SESSION['daily_report_start_date']) ? $_SESSION['daily_report_start_date'] : date('Y-m-d', strtotime("-7 days"));
    }
    
    public function set_end_date($v)
    {
        $_SESSION['daily_report_end_date'] = $v;
    }
    
    public function get_end_date()
    {
        return isset($_SESSION['daily_report_end_date']) ? $_SESSION['daily_report_end_date'] : date('Y-m-d', strtotime("-1 days"));
    }
    
    public function get() {
        $this->load->database();
        $sql = "
with
data_ua_date as(
select *
,case when lifetime>=1 then 1 else 0 end as retention_D1
,case when lifetime>=3 then 1 else 0 end as retention_D3
,case when lifetime>=7 then 1 else 0 end as retention_D7
,case when lifetime>=30 then 1 else 0 end as retention_D30
,case when last_active = trunc(dateadd(day, -2, trunc(sysdate))) then 1 else 0 end as retention_Lastday
from data_ua
where dates between '".$this->get_start_date()."'  and '".$this->get_end_date()."'  -- Tanggal  Start and end
    and user_country = '".$this->get_country()."'
),

data_revenue as
(select event_user
,round(sum(case when datediff(days,du.dates, di.event_date) <= 1 then price_iap else 0 end),2) as d1_revenue
,round(sum(case when datediff(days,du.dates, di.event_date) <= 7 then price_iap else 0 end),2) as d7_revenue
,round(sum(case when datediff(days,du.dates, di.event_date) <= 14 then price_iap else 0 end),2) as d14_revenue
,round(sum(case when datediff(days,du.dates, di.event_date) <= 30 then price_iap else 0 end),2) as d30_revenue
,round(sum(price_iap),2) as raw_revenue
from data_iap as di
join data_ua_date as du on di.event_user = du.swrve_user_id
group by event_user)


select dates, dates::varchar as referrer_name, dates::varchar as campaign_name, 0 as node
,round(sum(d1_revenue),2) as d1_revenue
,round(sum(d7_revenue),2) as d7_revenue
,round(sum(d14_revenue),2) as d14_revenue
,round(sum(d30_revenue),2) as d30_revenue
,round(sum(raw_revenue),2) as total_raw_revenue
,round(sum(revenue),2) as total_revenue
,round(sum(costs),2) as spend
,round(sum(costs)/count(1),2) as cpi
,count(1) as install
,(
    select organic from data_ua_organic 
    where data_ua_organic.dates = data_ua_date.dates
        and data_ua_organic.user_country = '".$this->get_country()."'
    ) as organic
,round(sum(revenue)/count(1),2) as arpu
,round(sum(raw_revenue)/count(1),2) as raw_arpu
,round(sum(revenue)/nullif(sum(spending_user),0),2) as arppu
,round(sum(raw_revenue)/nullif(sum(spending_user),0),2) as raw_arppu
,100*sum(spending_user)/count(1) as ppu
,round((sum(revenue)-sum(costs)),2) as roi
,round((sum(raw_revenue)-sum(costs)),2) as raw_roi
,nvl(100*round((sum(revenue)-sum(costs))/nullif(sum(costs),0),2),0) as roi_percent
,nvl(100*round((sum(raw_revenue)-sum(costs))/nullif(sum(costs),0),2),0) as raw_roi_percent
,nvl(100*round(sum(revenue)/nullif(sum(costs),0),2),0) as roas_percent
,nvl(100*round(sum(raw_revenue)/nullif(sum(costs),0),2),0) as raw_roas_percent
,sum(session)/count(1) as average_session
,sum(session_length)/count(1) as average_session_length
,sum(lifetime)/count(1) as average_lifetime
,100*sum(retention_D1)/count(1) as D1_retention
,100*sum(retention_D3)/count(1) as D3_retention
,100*sum(retention_D7)/count(1) as D7_retention
,100*sum(retention_D30)/count(1) as D30_retention
,100*sum(retention_Lastday)/count(1) as Lastday_retention
,coalesce(min(arpu_limit),-1) as arpu_limit
,coalesce(min(cpi_limit),-1) as cpi_limit
,coalesce(min(ppu_limit),-1) as ppu_limit
,coalesce(min(d1_limit),-1) as d1_limit
,coalesce(min(d3_limit),-1) as d3_limit
,coalesce(min(d7_limit),-1) as d7_limit
,coalesce(min(d30_limit),-1) as d30_limit
,coalesce(min(lastday_limit),-1) as lastday_limit

from data_ua_date 
left join data_revenue on data_ua_date.swrve_user_id = data_revenue.event_user
left join tbl_ua_setting on data_ua_date.referrer_name = tbl_ua_setting.channel 
    and tbl_ua_setting.project = 'billionaire'
group by dates

UNION ALL

select dates, referrer_name,referrer_name as campaign_name, 1 as node
,round(sum(d1_revenue),2) as d1_revenue
,round(sum(d7_revenue),2) as d7_revenue
,round(sum(d14_revenue),2) as d14_revenue
,round(sum(d30_revenue),2) as d30_revenue
,round(sum(raw_revenue),2) as total_raw_revenue
,round(sum(revenue),2) as total_revenue
,round(sum(costs),2) as spend
,round(sum(costs)/count(1),2) as cpi
,count(1) as install
,0 as organic
,round(sum(revenue)/count(1),2) as arpu
,round(sum(raw_revenue)/count(1),2) as raw_arpu
,round(sum(revenue)/nullif(sum(spending_user),0),2) as arppu
,round(sum(raw_revenue)/nullif(sum(spending_user),0),2) as raw_arppu
,100*sum(spending_user)/count(1) as ppu
,round((sum(revenue)-sum(costs)),2) as roi
,round((sum(raw_revenue)-sum(costs)),2) as raw_roi
,nvl(100*round((sum(revenue)-sum(costs))/nullif(sum(costs),0),2),0) as roi_percent
,nvl(100*round((sum(raw_revenue)-sum(costs))/nullif(sum(costs),0),2),0) as raw_roi_percent
,nvl(100*round(sum(revenue)/nullif(sum(costs),0),2),0) as roas_percent
,nvl(100*round(sum(raw_revenue)/nullif(sum(costs),0),2),0) as raw_roas_percent
,sum(session)/count(1) as average_session
,sum(session_length)/count(1) as average_session_length
,sum(lifetime)/count(1) as average_lifetime
,100*sum(retention_D1)/count(1) as D1_retention
,100*sum(retention_D3)/count(1) as D3_retention
,100*sum(retention_D7)/count(1) as D7_retention
,100*sum(retention_D30)/count(1) as D30_retention
,100*sum(retention_Lastday)/count(1) as Lastday_retention
,coalesce(min(arpu_limit),-1) as arpu_limit
,coalesce(min(cpi_limit),-1) as cpi_limit
,coalesce(min(ppu_limit),-1) as ppu_limit
,coalesce(min(d1_limit),-1) as d1_limit
,coalesce(min(d3_limit),-1) as d3_limit
,coalesce(min(d7_limit),-1) as d7_limit
,coalesce(min(d30_limit),-1) as d30_limit
,coalesce(min(lastday_limit),-1) as lastday_limit

from data_ua_date 
left join data_revenue on data_ua_date.swrve_user_id = data_revenue.event_user
left join tbl_ua_setting on data_ua_date.referrer_name = tbl_ua_setting.channel 
    and tbl_ua_setting.project = 'billionaire'
group by dates, referrer_name

UNION ALL

select dates, referrer_name,campaign_name, 2 as node
,round(sum(d1_revenue),2) as d1_revenue
,round(sum(d7_revenue),2) as d7_revenue
,round(sum(d14_revenue),2) as d14_revenue
,round(sum(d30_revenue),2) as d30_revenue
,round(sum(raw_revenue),2) as total_raw_revenue
,round(sum(revenue),2) as total_revenue
,round(sum(costs),2) as spend
,round(sum(costs)/count(1),2) as cpi
,count(1) as install
,0 as organic
,round(sum(revenue)/count(1),2) as arpu
,round(sum(raw_revenue)/count(1),2) as raw_arpu
,round(sum(revenue)/nullif(sum(spending_user),0),2) as arppu
,round(sum(raw_revenue)/nullif(sum(spending_user),0),2) as raw_arppu
,100*sum(spending_user)/count(1) as ppu
,round((sum(revenue)-sum(costs)),2) as roi
,round((sum(raw_revenue)-sum(costs)),2) as raw_roi
,nvl(100*round((sum(revenue)-sum(costs))/nullif(sum(costs),0),2),0) as roi_percent
,nvl(100*round((sum(raw_revenue)-sum(costs))/nullif(sum(costs),0),2),0) as raw_roi_percent
,nvl(100*round(sum(revenue)/nullif(sum(costs),0),2),0) as roas_percent
,nvl(100*round(sum(raw_revenue)/nullif(sum(costs),0),2),0) as raw_roas_percent
,sum(session)/count(1) as average_session
,sum(session_length)/count(1) as average_session_length
,sum(lifetime)/count(1) as average_lifetime
,100*sum(retention_D1)/count(1) as D1_retention
,100*sum(retention_D3)/count(1) as D3_retention
,100*sum(retention_D7)/count(1) as D7_retention
,100*sum(retention_D30)/count(1) as D30_retention
,100*sum(retention_Lastday)/count(1) as Lastday_retention
,-1 as arpu_limit
,-1 as cpi_limit
,-1 as ppu_limit
,-1 as d1_limit
,-1 as d3_limit
,-1 as d7_limit
,-1 as d30_limit
,-1 as lastday_limit

from data_ua_date 
left join data_revenue on data_ua_date.swrve_user_id = data_revenue.event_user

group by dates, referrer_name,campaign_name
order by dates, referrer_name, node, campaign_name
;
";
//        echo $sql; die;
        $query = $this->db->query($sql);        
        return $query->result_array();
    }

}
