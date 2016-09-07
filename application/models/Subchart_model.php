<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Subchart_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_start_date() {
        return isset($_SESSION['grid_start_date']) ? $_SESSION['grid_start_date'] : date('Y-m-d', strtotime("-7 days"));
    }

    public function get_end_date() {
        return isset($_SESSION['grid_end_date']) ? $_SESSION['grid_end_date'] : date('Y-m-d', strtotime("-1 days"));
    }

    public function get_referrer_name()
    {
        return isset($_SESSION['referrer_name']) ? $_SESSION['referrer_name'] : '';
    }
    
    public function get_campaign_name()
    {
        return isset($_SESSION['campaign_name']) ? $_SESSION['campaign_name'] : '';
    }
    
    public function set_data_AFSiteID($value) {
        $_SESSION['chart_data_AFSiteID'] = $value;
    }

    public function get_data_AFSiteID() {
        return isset($_SESSION['chart_data_AFSiteID']) ? $_SESSION['chart_data_AFSiteID'] : [];
    }

    public function get() {
        $arr_filter = $this->get_data_AFSiteID();
        foreach ($arr_filter as $v) {
            $arr_AFSiteID[] = "select '$v'::text as series";
        }
        $tanggal = $this->get_start_date();
        while (true) {
            $arr_tanggal[] = "select '$tanggal'::date as dates";
            if ($tanggal == $this->get_end_date()) {
                break;
            } else {
                $tanggal = date('Y-m-d', strtotime("$tanggal +1 days"));
            }
        }
        
        $sql_sub_where = implode("','", $arr_filter);
//        $subquery_tanggal = implode(" union \r\n", $arr_tanggal);
//        $subquery_AFSiteID = implode(" union \r\n", $arr_AFSiteID);
        
        $sql = "
select dcf.* 
  ,COALESCE((select event_note from tbl_ua_manage_note where tanggal = dcf.dates and project = 'billionaire' limit 1), '') event_note
from data_chart_AFSiteID dcf
where dates>= '".$this->get_start_date()."' -- Tanggal Start
and dates<='".$this->get_end_date()."' -- Tanggal end
and referrer_name='".$this->get_referrer_name()."' -- Filter referrer name 
and campaign_name='".$this->get_campaign_name()."' -- Filter campaign name
and AFSiteID IN ('$sql_sub_where')  -- Selected AFSiteID
order by dates, AFSiteID";
        
/*
       $sql = "
with 
data_ua as
(select media_source as refferer_name,af_siteid as AFSiteID, campaign as campaign_name,date_joined::date as dates
from appsflyer_ios_in_app_event_non_organic a 
LEFT JOIN swrve_properties_ios sp  ON sp.swrve_user_id=a.customer_user_id 
where a.event_type='install'
and date_joined::date>= '".$this->get_start_date()."' -- Tanggal Start
and date_joined::date<='".$this->get_end_date()."' -- Tanggal end
and media_source='".$this->get_referrer_name()."' and campaign='".$this->get_campaign_name()."' -- Filter refferer name dan campaign name
),

data_chart_final as (
select dates, AFSiteID series,count(1) as install
from data_ua 
where AFSiteID IN ('$sql_sub_where')
group by  dates, AFSiteID
),

data_tanggal as (
$subquery_tanggal
),

data_AFSiteID as (
$subquery_AFSiteID
),

data_tanggal_AFSiteID as (
select * from data_tanggal, data_AFSiteID
)

select dta.* 
  ,COALESCE((select event_note from tbl_ua_manage_note where tanggal = dta.dates limit 1), '') event_note
  ,COALESCE(dcf.install, 0) install
from data_tanggal_AFSiteID dta 
left join data_chart_final dcf
  on dta.dates = dcf.dates and dta.series = dcf.series
order by dta.dates, dta.series

";
 * 
 */
        
//echo $sql;
//die;
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }

}
