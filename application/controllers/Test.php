<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {

        $sql = "--  Chart Refferer Name

with 
data_ua as
(select af_cost_currency,af_cost_model,af_cost_value,af_siteid,media_source,campaign,
date_joined,last_active,datediff(days,date_joined,last_active) as lifetime,milliseconds_played,spend,
date_joined::date as dates
from appsflyer_ios_in_app_event_non_organic a
LEFT JOIN swrve_properties_ios sp ON a.customer_user_id=sp.swrve_user_id 
where a.event_type='install'
),


data_ua_date as(
select dates,media_source as refferer_name,af_siteid as sub_campaign, campaign as campaign_name
,spend::REAL/100 as rev
,count(1) over(partition by dates) as non_organic_all
from data_ua 
where dates>= '2016-05-20' -- Tanggal Start
and dates<= '2016-05-29' -- Tanggal end
)


select dates, refferer_name,refferer_name as campaign_name
,max(non_organic_all) as non_organic_install
,count(1) as install
,sum(rev) as revenue
,sum(rev)/count(1) as arpu
from data_ua_date 
group by  dates, refferer_name

UNION
select dates, refferer_name,campaign_name
,max(non_organic_all) as non_organic_install
,count(1) as install
,sum(rev) as revenue
,sum(rev)/count(1) as arpu
from data_ua_date 
group by  dates, refferer_name, campaign_name
;";

        $query = $this->db->query($sql);

        echo "<h1>Testing from codeigniter</h1>";
        echo "<table>\n";
        foreach ($query->result_array() as $row) {
            echo "\t<tr>\n";
            foreach ($row as $value) {
                echo "\t\t<td>$value</td>\n";
            }
            echo "\t</tr>\n";
        }
        echo "</table>\n";
    }

}
