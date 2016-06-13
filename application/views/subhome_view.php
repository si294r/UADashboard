<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url('assets/favicon-32x32.png') ?>">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Dashboard</title>

        <link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/navbar.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/datepicker/css/datepicker.css') ?>" rel="stylesheet">

        <link href="<?php echo base_url('../ext-6.0.1/build/classic/theme-gray/resources/theme-gray-all.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('../ext-6.0.1/build/examples/classic/restful/restful.css') ?>" rel="stylesheet">

        <!-- Core -->
        <script type="text/javascript" src="<?php echo base_url('assets/jquery/jquery-2.2.2.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('../ext-6.0.1/build/ext-all.js') ?>"></script>
        <!-- Dependency: jquery -->
        <script type="text/javascript" src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js') ?>"></script>
        <script type="text/javascript" src="https://code.highcharts.com/highcharts.js"></script>
        <!-- Dependency: jquery, bootstrap -->
        <script type="text/javascript" src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js') ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/js.cookie.js') ?>"></script>

        <script type="text/javascript">
            document.app_url = '<?php echo base_url() ?>';
            Ext.onReady(function () {
                Ext.getBody().removeCls('x-body'); // stop extjs from overriding bootstrap css - 2016-06-03
            });
        </script>
        <script type="text/javascript">

            function reload_chart(url) {
                Ext.get('chart_container').mask('loading');

                $.getJSON(url, function (data) {

                    var i = 0;
                    var arr_yAxis = [];
                    var arr_series = [];
                    for (var series in data.yAxis) {
                        var series_name = series.substring(series.indexOf(",") + 1).replace(/_/g, " ");
                        arr_yAxis.push({
                            labels: {
                                style: {
                                    color: Highcharts.getOptions().colors[i]
                                }
                            },
                            title: {
                                text: series_name,
                                style: {
                                    color: Highcharts.getOptions().colors[i]
                                }
                            },
//                            opposite: series == 'Non_Organic_Install' ? true : false
//                            opposite: false
                        });
                        arr_series.push({
                            name: series_name,
//                            type: 'spline',
//                            yAxis: series == 'Non_Organic_Install' ? 0 : 1,
//                            yAxis: 1,
                            data: data.yAxis[series]
                        });
                        i++;
                    }

                    Ext.get('chart_container').unmask();

                    $('#chart_container').highcharts({
                        chart: {
                            type: 'spline'
                        },
                        title: {
                            text: ''
                        },
                        subtitle: {
                            text: 'Start Date:' + data.start_date + '.End Date: ' + data.end_date
                        },
                        xAxis: [{
                                categories: data.xAxis,
                                crosshair: true
                            }],
                        yAxis: arr_yAxis,
                        tooltip: {
                            shared: true
                        },
                        legend: {
                            enabled: false
                        },
                        series: arr_series
                    }, function (chart) { // on complete
                        for (var i = 0; i < data.note.length; i++) {
                            if (data.note[i] != '') {
                                var point = chart.series[0].points[i];
                                var teks = data.note[i];
                                var html_teks = '';
                                for (var j = 0; j < teks.length; j++) {
                                    html_teks += teks.charAt(j) + '<br/>';
                                }
                                html_teks = html_teks.replace(/ /g, '<span style="color:white">.</span>');
                                chart.renderer.text(html_teks, point.plotX + chart.plotLeft - 3, 5 + chart.plotTop)
                                        .css({
                                            'color': 'red',
                                            'font-size': '10px'
                                        })
                                        .add().toFront();
                            }
                        }
                    });
                });
            }

            function reload_grid(url) {
                with (Ext.getCmp('treePanel').getStore()) {
                    getProxy().setUrl(url);
                    removeAll();
                    load();
                }
            }

            function reload_by_date(val) {
                if (val == 0) {
                    var start_date = $('#start_date').val();
                    var end_date = $('#end_date').val();
                    Ext.get('chart_container').mask('loading');
                    reload_grid(document.app_url + 'subhome/grid/' + val + '/' + start_date + '/' + end_date);
                } else {
                    Ext.get('chart_container').mask('loading');
                    reload_grid(document.app_url + 'subhome/grid/' + val);
                }
            }

            function reload_by_selected_grid() {
                Ext.getCmp('treePanel').getSelectionModel().selectRange(0, parseInt($('#show_top').val()) - 1);
            }

            function reload_chart_by_selection() {
                var obj = Ext.getCmp('treePanel').getSelection();
                if (obj.length > 0) {
                    var param = [];
                    for (var i = 0; i < obj.length; i++) {
                        param.push(obj[i].data.afsiteid);
                    }
                    reload_chart(document.app_url + 'subhome/chart/' + encodeURIComponent(JSON.stringify(param)));
                }
            }

            $(document).ready(function () {

                $('#btn_search').click(function () {
                    reload_by_date(0);
                });
                $('#lbtn_lastweek').click(function () {
                    reload_by_date(1);
                });
                $('#lbtn_last2week').click(function () {
                    reload_by_date(2);
                });
                $('#lbtn_lastmonth').click(function () {
                    reload_by_date(3);
                });
                $('#lbtn_last3month').click(function () {
                    reload_by_date(4);
                });
                $('#graphic1').change(function () {
                    Cookies.set('submain_graphic1', $('#graphic1').val());
                    reload_chart_by_selection();
                });
                $('#show_top').change(function () {
                    Cookies.set('submain_show_top', $('#show_top').val());
                    reload_by_selected_grid();
                });

                if (typeof (Cookies.get('submain_graphic1')) !== 'undefined') {
                    $('#graphic1').val(Cookies.get('submain_graphic1'));
                }
                if (typeof (Cookies.get('submain_show_top')) !== 'undefined') {
                    $('#show_top').val(Cookies.get('submain_show_top'));
                }

                $('#btnBackToMain').click(function () {
                    location.href = document.app_url + 'home';
                });

            });
        </script>        
        <script>
            Ext.require([
                'Ext.data.*',
                'Ext.grid.*',
                'Ext.tree.*',
                'Ext.tip.*',
                'Ext.ux.CheckColumn'
            ]);

            Ext.define('Task', {
                extend: 'Ext.data.TreeModel',
                fields: [
                    {name: 'afsiteid', type: 'string'},
                    {name: 'total_revenue', type: 'number'},
                    {name: 'cpi', type: 'number'},
                    {name: 'install', type: 'number'},
                    {name: 'arpu', type: 'number'},
                    {name: 'arppu', type: 'number'},
                    {name: 'ppu', type: 'number'},
                    {name: 'roi', type: 'number'},
                    {name: 'average_session', type: 'number'},
                    {name: 'average_session_length', type: 'number'},
                    {name: 'average_lifetime', type: 'number'},
                    {name: 'd1_retention', type: 'number'},
                    {name: 'd3_retention', type: 'number'},
                    {name: 'd7_retention', type: 'number'},
                    {name: 'modus_businesstier', type: 'number'},
                    {name: 'median_businesstier', type: 'number'},
                    {name: 'mean_crystaluse', type: 'number'},
                    {name: 'median_crystaluse', type: 'number'},
                    {name: 'show', type: 'boolean'}
                ]
            });

            Ext.onReady(function () {
                Ext.tip.QuickTipManager.init();

                var store = Ext.create('Ext.data.TreeStore', {
                    model: 'Task',
                    autoLoad: false,
                    proxy: {
                        type: 'ajax',
                        url: document.app_url + 'subhome/grid'
                    },
                    listeners: {
                        load: function () {
                            reload_by_selected_grid();
                        }
                    },
                    sorters: [{
                            property: 'total_revenue',
                            direction: 'DESC'
                        }],
                    folderSort: true
                });

                Ext.create('Ext.tree.Panel', {
                    id: 'treePanel',
                    height: 400,
                    renderTo: Ext.getElementById('grid_container'),
                    collapsible: false,
                    useArrows: true,
                    rootVisible: false,
                    store: store,
                    selModel: {
                        selType: 'checkboxmodel',
                        checkOnly: true
                    },
                    listeners: {
                        selectionchange: {
                            fn: function () {
                                reload_chart_by_selection();
                            }
                        }
                    },
                    columns: [{
                            //xtype: 'treecolumn',
                            text: 'AF-SiteID',
                            width: 200,
                            sortable: true,
                            dataIndex: 'afsiteid',
                            locked: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'Revenue',
                            width: 80,
                            dataIndex: 'total_revenue',
                            tpl: Ext.create('Ext.XTemplate', '{total_revenue:this.formatTemplate}', {
                                formatTemplate: function (v) {
                                    return '$' + v;
                                }
                            }),
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'CPI',
                            width: 80,
                            dataIndex: 'cpi',
                            tpl: Ext.create('Ext.XTemplate', '{cpi:this.formatTemplate}', {
                                formatTemplate: function (v) {
                                    return '$' + v;
                                }
                            }),
                            sortable: true
                        }, {
                            text: 'Install',
                            width: 80,
                            dataIndex: 'install',
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'ARPU',
                            width: 80,
                            dataIndex: 'arpu',
                            tpl: Ext.create('Ext.XTemplate', '{arpu:this.formatTemplate}', {
                                formatTemplate: function (v) {
                                    return '$' + v;
                                }
                            }),
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'ARPPU',
                            width: 80,
                            dataIndex: 'arppu',
                            tpl: Ext.create('Ext.XTemplate', '{arppu:this.formatTemplate}', {
                                formatTemplate: function (v) {
                                    return '$' + v;
                                }
                            }),
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'PPU',
                            width: 80,
                            dataIndex: 'ppu',
                            tpl: Ext.create('Ext.XTemplate', '{ppu:this.formatTemplate}', {
                                formatTemplate: function (v) {
                                    return v + '%';
                                }
                            }),
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'ROI',
                            width: 80,
                            dataIndex: 'roi',
                            tpl: Ext.create('Ext.XTemplate', '{roi:this.formatTemplate}', {
                                formatTemplate: function (v) {
                                    return '$' + v;
                                }
                            }),
                            sortable: true
                        }, {
                            text: 'Average Session',
                            width: 110,
                            dataIndex: 'average_session',
                            sortable: true
                        }, {
                            text: 'Average Session Length',
                            width: 140,
                            dataIndex: 'average_session_length',
                            sortable: true
                        }, {
                            text: 'Average Lifetime',
                            width: 110,
                            dataIndex: 'average_lifetime',
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'D1 Retention',
                            width: 80,
                            dataIndex: 'd1_retention',
                            tpl: Ext.create('Ext.XTemplate', '{d1_retention:this.formatTemplate}', {
                                formatTemplate: function (v) {
                                    return v + '%';
                                }
                            }),
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'D3 Retention',
                            width: 80,
                            dataIndex: 'd3_retention',
                            tpl: Ext.create('Ext.XTemplate', '{d3_retention:this.formatTemplate}', {
                                formatTemplate: function (v) {
                                    return v + '%';
                                }
                            }),
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'D7 Retention',
                            width: 80,
                            dataIndex: 'd7_retention',
                            tpl: Ext.create('Ext.XTemplate', '{d7_retention:this.formatTemplate}', {
                                formatTemplate: function (v) {
                                    return v + '%';
                                }
                            }),
                            sortable: true
                        }, {
                            text: 'Modus BTier',
                            width: 80,
                            dataIndex: 'modus_businesstier',
                            sortable: true
                        }, {
                            text: 'Median BTier',
                            width: 80,
                            dataIndex: 'median_businesstier',
                            sortable: true
                        }, {
                            text: 'Mean Crystal Usage',
                            width: 130,
                            dataIndex: 'mean_crystaluse',
                            sortable: true
                        }, {
                            text: 'Median Crystal Usage',
                            width: 140,
                            dataIndex: 'median_crystaluse',
                            sortable: true
                        }]
                });

            });

            Ext.onReady(function () {
                Ext.get('chart_container').mask('loading');
                reload_grid(document.app_url + 'subhome/grid');
            });
        </script>        
    </head>
    <body style="margin-bottom: 100px;">

        <div class="container">

            <!-- Static navbar -->
            <nav class="navbar navbar-default" style="margin-bottom: 10px;">
                <div class="container-fluid">
                    <div class="navbar-header">                        
                        <a class="navbar-brand" href="#">UA</a>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav">
                            <?php $class = strtolower($this->router->fetch_class()); ?>
                            <li class="<?php echo $class == 'home' ? 'active' : '' ?>">
                                <a href="<?php echo base_url('home') ?>">Dashboard</a>
                            </li>
                            <li class="<?php echo $class == 'setting' ? 'active' : '' ?>">
                                <a href="<?php echo base_url('setting') ?>">Setting</a>
                            </li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="<?php echo base_url('signin/out') ?>">Signout</a></li>
                        </ul>                        
                    </div><!--/.nav-collapse -->
                </div><!--/.container-fluid -->
            </nav>

            <div class="row" style="margin-top: 0px;">
                <div class="col-md-8"><h3><?php echo $referrer_name . " - " . $campaign_name; ?></h3></div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="col-md-8">
                    <form class="form-inline">
                        <div class="form-group">
                            <label for="start_date">From</label>
                            <input type="text" class="form-control" id="start_date">
                        </div>
                        <div class="form-group">
                            <label for="end_date">To</label> 
                            <input type="text" class="form-control" id="end_date">
                        </div>
                        <script type="text/javascript">
                            $(function () {
                                $('#start_date').datepicker({ format: 'yyyy-mm-dd' });
                                $('#end_date').datepicker({ format: 'yyyy-mm-dd' });
                            });
                        </script>                        
                        <button type="button" class="btn" id="btn_search">Search</button>                        
                    </form>
                </div>
                <div class="col-md-4" style="text-align:right;">
                    <a id="lbtn_lastweek">Last Week</a> |
                    <a id="lbtn_last2week">Last 2 Weeks</a> |
                    <a id="lbtn_lastmonth">Last Month</a> |
                    <a id="lbtn_last3month">Last 3 Months</a>
                </div>
            </div>

            <div class="row" style="margin-top: 20px;">
                <div class="col-md-12" id="chart_container" style="height: 450px;"></div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="col-md-4" style="text-align: center;">
                    <form class="form-inline">
                        <div class="form-group">
                            <label for="graphic1">Graphic 1</label>
                            <select class="form-control" id="graphic1" style="width: 150px">
                                <option value="Install" selected>Install</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="col-md-4" style="text-align: center;">
                    <form class="form-inline">
                        <div class="form-group">
                            <label for="show_top">Show Top</label>
                            <select class="form-control" id="show_top" style="width: 60px">
                                <option value="1">1</option>
                                <option value="3">3</option>
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                            </select>
                            <label>AF-SiteID</label>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row" style="margin-top: 20px;">
                <div class="col-md-12" id="grid_container"></div>
            </div>

            <div class="row" style="margin-top: 20px;">
                <div class="col-md-4">
                    <button type="button" class="btn btn-default" id="btnBackToMain">Back To Main</button>
                </div>
            </div>
        </div>

    </body>
</html>