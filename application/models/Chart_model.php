<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Chart_model extends CI_Model {

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

    public function set_data_referrer($value) {
        $_SESSION['chart_data_referrer'] = $value;
    }

    public function get_data_referrer() {
        return isset($_SESSION['chart_data_referrer']) ? $_SESSION['chart_data_referrer'] : [];
    }

    public function get() {
        $arr_filter = $this->get_data_referrer();
        foreach ($arr_filter as $v) {
            $arr_series[] = "select '$v'::text as series";
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
        
        $sql_where = implode("','", $arr_filter);
        $subquery_tanggal = implode(" union \r\n", $arr_tanggal);
        $subquery_series = implode(" union \r\n", $arr_series);
        
        $sql = "with 
data_ua as
(select af_siteid,media_source,campaign,date_joined::date as dates,
case when media_source is null then 0 else 1 end as non_organic
from swrve_properties_ios sp 
LEFT JOIN appsflyer_ios_in_app_event_non_organic a ON sp.swrve_user_id=a.customer_user_id 
where (event_type='install' or event_type is null)
and date_joined::date>= '" . $this->get_start_date() . "'  -- Tanggal Start
and date_joined::date<= '" . $this->get_end_date() . "'  -- Tanggal end
),

data_all_install as
(select dates
,sum(non_organic) as all_non_organic
,count(1) as all_user
from data_ua 
group by dates
),

data_ua_date as(
select dates,media_source as referrer_name,af_siteid as sub_campaign, campaign as campaign_name
from data_ua 
where  referrer_name is not null
),

data_chart as (
select dates, referrer_name,referrer_name as campaign_name
,count(1) as install
from data_ua_date
group by dates, referrer_name

UNION
select dates, referrer_name,campaign_name
,count(1) as install
from data_ua_date
group by  dates, referrer_name, campaign_name
),

data_chart_final as (
select dates
,referrer_name + ',' + campaign_name series 
,max(install) install  
from data_chart
where referrer_name + ',' + campaign_name IN ('$sql_where')
group by dates, series
),

data_tanggal as (
$subquery_tanggal
),

data_series as (
$subquery_series
),

data_tanggal_series as (
select * from data_tanggal, data_series
)

select dts.* 
  ,COALESCE((select event_note from tbl_ua_manage_note where tanggal = dts.dates limit 1), '') event_note
--  ,COALESCE(MAX(dcf.non_organic_install) over (partition by dts.dates), 0) non_organic_install
--   ,COALESCE(dcf.non_organic_install, 0) non_organic_install
  ,COALESCE(dcf.install, 0) install
  ,COALESCE(dai.all_user, 0) all_install
  ,COALESCE(dai.all_non_organic, 0) non_organic_install
from data_tanggal_series dts 
left join data_chart_final dcf
  on dts.dates = dcf.dates and dts.series = dcf.series
left join data_all_install dai
  on dts.dates = dai.dates
order by dts.dates, dts.series

";
//echo $sql;
//die;

        $query = $this->db->query($sql);
        return $query->result_array();
    }

}
