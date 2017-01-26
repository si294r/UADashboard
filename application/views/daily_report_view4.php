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

        <link href="https://cdnjs.cloudflare.com/ajax/libs/extjs/6.0.1/classic/theme-gray/resources/theme-gray-all.css" rel="stylesheet">
        <link href="<?php echo base_url('assets/restful.css') ?>" rel="stylesheet">

        <style>
            .grid-row-green {
                background-color: #99e699;
            }
            .grid-cell-yellow {
                background-color: #ffff66;
            }
        </style>

        <!-- Core -->
        <script type="text/javascript" src="<?php echo base_url('assets/jquery/jquery-2.2.2.min.js') ?>"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/extjs/6.0.1/ext-all.js"></script>
        <!-- Dependency: jquery -->
        <script type="text/javascript" src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js') ?>"></script>
        <!-- Dependency: jquery, bootstrap -->
        <script type="text/javascript" src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js') ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/js.cookie.js') ?>"></script>

        <script type="text/javascript">
            document.app_url = '<?php echo base_url() ?>';
            document.app_class = '<?php echo strtolower($this->router->fetch_class()); ?>';
            Ext.onReady(function () {
                Ext.getBody().removeCls('x-body'); // stop extjs from overriding bootstrap css - 2016-06-03
            });
        </script>

        <script type="text/javascript">

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
                    reload_grid(document.app_url + document.app_class + '/grid/' + val + '/' + start_date + '/' + end_date);
                } else {
                    reload_grid(document.app_url + document.app_class + '/grid/' + val);
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
                
                $('#country').change(function () {
                    location.href = document.app_url + document.app_class + '/index/' + $('#country').val();
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
                    {name: 'referrer_name', type: 'string'},
                    {name: 'campaign_name', type: 'string'},
                    {name: 'd1_revenue', type: 'number'},
                    {name: 'd7_revenue', type: 'number'},
                    {name: 'd14_revenue', type: 'number'},
                    {name: 'd30_revenue', type: 'number'},
                    {name: 'total_raw_revenue', type: 'number'},
                    {name: 'total_revenue', type: 'number'},
                    {name: 'spend', type: 'number'},
                    {name: 'cpi', type: 'number'},
                    {name: 'install', type: 'number'},
                    {name: 'organic', type: 'number'},
                    {name: 'arpu', type: 'number'},
                    {name: 'raw_arpu', type: 'number'},
                    {name: 'arppu', type: 'number'},
                    {name: 'raw_arppu', type: 'number'},
                    {name: 'ppu', type: 'number'},
                    {name: 'roi', type: 'number'},
                    {name: 'raw_roi', type: 'number'},
                    {name: 'roi_percent', type: 'number'},
                    {name: 'raw_roi_percent', type: 'number'},
                    {name: 'roas_percent', type: 'number'},
                    {name: 'raw_roas_percent', type: 'number'},
                    {name: 'average_session', type: 'number'},
                    {name: 'average_session_length', type: 'number'},
                    {name: 'average_lifetime', type: 'number'},
                    {name: 'd1_retention', type: 'number'},
                    {name: 'd3_retention', type: 'number'},
                    {name: 'd7_retention', type: 'number'},
                    {name: 'd30_retention', type: 'number'},
                    {name: 'lastday_retention', type: 'number'},
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
                        url: document.app_url + document.app_class + '/grid'
                    },
                    listeners: {
                        load: function (obj, records, successful, operation, node, eOpts) {
                            $.getJSON(document.app_url + document.app_class + '/get_session_report', function(data) {
                                //console.log(data);
                                $('#start_date').val(data.start_date);
                                $('#end_date').val(data.end_date);
                                $('#country').val(data.country);
                            });
                        }
                    },
                    sorters: [{
                            property: 'campaign_name',
                            direction: 'ASC'
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
                            }
                        },
                        itemdblclick: {
                            fn: function (obj, record, item, index, e, eOpts) {
                            }
                        }
                    },
                    viewConfig: {
                        getRowClass: function (record, rowIndex, rowParams, store) {
                            return record.get("total_revenue") > record.get("spend") ? "grid-row-green" : "";
                        }
                    },
                    columns: [{
                            xtype: 'treecolumn',
                            text: 'Install Date',
                            width: 200,
                            sortable: true,
                            dataIndex: 'campaign_name',
                            locked: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'D1 Revenue',
                            width: 95,
                            dataIndex: 'd1_revenue',
                            tpl: Ext.create('Ext.XTemplate', '{d1_revenue:this.formatTemplate}', {
                                formatTemplate: function (v) {
                                    return '$' + v;
                                }
                            }),
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'D7 Revenue',
                            width: 95,
                            dataIndex: 'd7_revenue',
                            tpl: Ext.create('Ext.XTemplate', '{d7_revenue:this.formatTemplate}', {
                                formatTemplate: function (v) {
                                    return '$' + v;
                                }
                            }),
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'D14 Revenue',
                            width: 95,
                            dataIndex: 'd14_revenue',
                            tpl: Ext.create('Ext.XTemplate', '{d14_revenue:this.formatTemplate}', {
                                formatTemplate: function (v) {
                                    return '$' + v;
                                }
                            }),
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'D30 Revenue',
                            width: 95,
                            dataIndex: 'd30_revenue',
                            tpl: Ext.create('Ext.XTemplate', '{d30_revenue:this.formatTemplate}', {
                                formatTemplate: function (v) {
                                    return '$' + v;
                                }
                            }),
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'Today Revenue',
                            width: 110,
                            dataIndex: 'total_raw_revenue',
                            tpl: Ext.create('Ext.XTemplate', '{total_raw_revenue:this.formatTemplate}', {
                                formatTemplate: function (v) {
                                    return '$' + v;
                                }
                            }),
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'Spend',
                            width: 80,
                            dataIndex: 'spend',
                            tpl: Ext.create('Ext.XTemplate', '{spend:this.formatTemplate}', {
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
                            tpl: Ext.create('Ext.XTemplate', '{[this.formatTemplate(values)]}', {
                                formatTemplate: function (v) {
                                    if (v.cpi_limit > -1 && v.cpi > v.cpi_limit) {
                                        return '<font style="color: red;">$' + v.cpi + '</font>';
                                    } else {
                                        return '$' + v.cpi;
                                    }
                                }
                            }),
                            sortable: true
                        }, {
                            text: 'Install',
                            width: 80,
                            dataIndex: 'install',
//                            tdCls: 'grid-cell-yellow',
                            sortable: true
                        }, {
                            text: 'Organic',
                            width: 80,
                            dataIndex: 'organic',
//                            tdCls: 'grid-cell-yellow',
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'ARPU',
                            width: 80,
                            dataIndex: 'raw_arpu',
                            tpl: Ext.create('Ext.XTemplate', '{[this.formatTemplate(values)]}', {
                                formatTemplate: function (v) {
                                    if (v.arpu_limit > -1 && v.raw_arpu < v.arpu_limit) {
                                        return '<font style="color: red;">$' + v.raw_arpu + '</font>';
                                    } else {
                                        return '$' + v.raw_arpu;
                                    }
                                }
                            }),
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'ARPPU',
                            width: 80,
                            dataIndex: 'raw_arppu',
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
                            tpl: Ext.create('Ext.XTemplate', '{[this.formatTemplate(values)]}', {
                                formatTemplate: function (v) {
                                    if (v.ppu_limit > -1 && v.ppu < v.ppu_limit) {
                                        return '<font style="color: red;">' + v.ppu + '%</font>';
                                    } else {
                                        return v.ppu + '%';
                                    }
                                }
                            }),
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'ROI',
                            width: 80,
                            dataIndex: 'raw_roi',
                            tpl: Ext.create('Ext.XTemplate', '{raw_roi:this.formatTemplate}', {
                                formatTemplate: function (v) {
                                    return '$' + v;
                                }
                            }),
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'ROI%',
                            width: 80,
                            dataIndex: 'raw_roi_percent',
                            tpl: Ext.create('Ext.XTemplate', '{raw_roi_percent:this.formatTemplate}', {
                                formatTemplate: function (v) {
                                    return v + '%';
                                }
                            }),
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'ROAS%',
                            width: 80,
                            dataIndex: 'raw_roas_percent',
                            tpl: Ext.create('Ext.XTemplate', '{raw_roas_percent:this.formatTemplate}', {
                                formatTemplate: function (v) {
                                    return v + '%';
                                }
                            }),
                            sortable: true
                        }, {
                            text: 'Average Number of Session',
                            width: 170,
                            dataIndex: 'average_session',
                            sortable: true
                        }, {
                            text: 'Average Session Length (in minutes)',
                            width: 200,
                            dataIndex: 'average_session_length',
                            sortable: true
                        }, {
                            text: 'Average Lifetime (in days)',
                            width: 155,
                            dataIndex: 'average_lifetime',
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'D1 Retention',
                            width: 80,
                            dataIndex: 'd1_retention',
                            tpl: Ext.create('Ext.XTemplate', '{[this.formatTemplate(values)]}', {
                                formatTemplate: function (v) {
                                    if (v.d1_limit > -1 && v.d1_retention < v.d1_limit) {
                                        return '<font style="color: red;">' + v.d1_retention + '%</font>';
                                    } else {
                                        return v.d1_retention + '%';
                                    }
                                }
                            }),
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'D3 Retention',
                            width: 80,
                            dataIndex: 'd3_retention',
                            tpl: Ext.create('Ext.XTemplate', '{[this.formatTemplate(values)]}', {
                                formatTemplate: function (v) {
                                    if (v.d3_limit > -1 && v.d3_retention < v.d3_limit) {
                                        return '<font style="color: red;">' + v.d3_retention + '%</font>';
                                    } else {
                                        return v.d3_retention + '%';
                                    }
                                }
                            }),
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'D7 Retention',
                            width: 80,
                            dataIndex: 'd7_retention',
                            tpl: Ext.create('Ext.XTemplate', '{[this.formatTemplate(values)]}', {
                                formatTemplate: function (v) {
                                    if (v.d7_limit > -1 && v.d7_retention < v.d7_limit) {
                                        return '<font style="color: red;">' + v.d7_retention + '%</font>';
                                    } else {
                                        return v.d7_retention + '%';
                                    }
                                }
                            }),
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'D30 Retention',
                            width: 100,
                            dataIndex: 'd30_retention',
                            tpl: Ext.create('Ext.XTemplate', '{[this.formatTemplate(values)]}', {
                                formatTemplate: function (v) {
                                    if (v.d30_limit > -1 && v.d30_retention < v.d30_limit) {
                                        return '<font style="color: red;">' + v.d30_retention + '%</font>';
                                    } else {
                                        return v.d30_retention + '%';
                                    }
                                }
                            }),
                            sortable: true
                        }, {
                            xtype: 'templatecolumn',
                            text: 'Last Day Retention',
                            width: 120,
                            dataIndex: 'lastday_retention',
                            tpl: Ext.create('Ext.XTemplate', '{[this.formatTemplate(values)]}', {
                                formatTemplate: function (v) {
                                    if (v.lastday_limit > -1 && v.lastday_retention < v.lastday_limit) {
                                        return '<font style="color: red;">' + v.lastday_retention + '%</font>';
                                    } else {
                                        return v.lastday_retention + '%';
                                    }
                                }
                            }),
                            sortable: true
                        }]
                });

            });

            Ext.onReady(function () {
                reload_grid(document.app_url + document.app_class + '/grid');
            });
        </script>        
    </head>
    <body style="margin-bottom: 100px;">

        <div class="container">

            <!-- Static navbar -->
            <?php $this->load->view ('navbar'); ?>

            <div class="row" style="margin-top: 0px;">
                <div class="col-md-8"><h3>Almighty 1.5 - Daily Report</h3></div>
            </div>

            <div class="row" style="margin-top: 10px;">
                <div class="col-md-6">
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
                                $('#start_date').datepicker({format: 'yyyy-mm-dd'});
                                $('#end_date').datepicker({format: 'yyyy-mm-dd'});
                            });
                        </script>                        
                        <button type="button" class="btn" id="btn_search">Search</button>                        
                    </form>
                </div>
                <div class="col-md-2" style="text-align: center;">
                    <form class="form-inline">
                        <div class="form-group">
                            <label for="country">Country</label>
                            <select class="form-control" id="country" style="width: 60px">
                                <option value="ALL">ALL</option>
                                <?php 
                                foreach ($arr_country as $value) {
                                    echo "<option value=\"{$value['user_country']}\">{$value['user_country']}</option>";                                    
                                }
                                ?>
                            </select>
                        </div>
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
                <div class="col-md-12" id="grid_container"></div>
            </div>
        </div>

    </body>
</html>