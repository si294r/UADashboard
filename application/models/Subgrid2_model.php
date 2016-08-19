<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Subgrid2_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }    
    
    public function set_start_date($v)
    {
        $_SESSION['grid2_start_date'] = $v;
    }
    
    public function get_start_date()
    {
        return isset($_SESSION['grid2_start_date']) ? $_SESSION['grid2_start_date'] : date('Y-m-d', strtotime("-7 days"));
    }
    
    public function set_end_date($v)
    {
        $_SESSION['grid2_end_date'] = $v;
    }
    
    public function get_end_date()
    {
        return isset($_SESSION['grid2_end_date']) ? $_SESSION['grid2_end_date'] : date('Y-m-d', strtotime("-1 days"));
    }
    
    public function get_referrer_name()
    {
        return isset($_SESSION['referrer_name2']) ? $_SESSION['referrer_name2'] : '';
    }
    
    public function get_campaign_name()
    {
        return isset($_SESSION['campaign_name2']) ? $_SESSION['campaign_name2'] : '';
    }
    
    public function get() {
        $sql = "
with 
data_ua_date as(
select *
,case when lifetime>=1 then 1 else 0 end as retention_D1
,case when lifetime>=3 then 1 else 0 end as retention_D3
,case when lifetime>=7 then 1 else 0 end as retention_D7
from data_ua_jellypop
where dates between '".$this->get_start_date()."'  and '".$this->get_end_date()."'  -- Tanggal  Start and end
and referrer_name='".$this->get_referrer_name()."' and campaign_name='".$this->get_campaign_name()."' -- Filter referrer name dan campaign name
)


select AFSiteID
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

from data_ua_date 
group by AFSiteID
order by total_revenue desc
;
";
//        echo $sql;
//        die;
        $query = $this->db->query($sql);        
        return $query->result_array();
    }

}
