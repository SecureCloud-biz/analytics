google.load("visualization","1",{packages:["corechart","table","geochart"]});AnalyticsField=Garnish.Base.extend({init:function(e){console.log("fieldId",e);this.$element=$("#"+e);this.$field=$(".analytics-field",this.$element);this.$spinner=$(".spinner",this.$element);this.$errorElement=$(".error",this.$element);this.$metricElement=$(".analytics-metric select",this.$element);this.$chartElement=$(".chart",this.$element);this.$elementId=$(".analytics-field",this.$element).data("element-id");this.$chart=!1;var t=this;this.$metricElement.change(function(){t.$metric=$(this).val();t.request()});this.$metricElement.trigger("change")},request:function(){var e=new google.visualization.DataTable,t={};this.$spinner.removeClass("hidden");Craft.postActionRequest("analytics/elementReport",{id:this.$elementId,metric:this.$metric},$.proxy(function(n){this.$spinner.addClass("hidden");if(typeof n.error!="undefined"){this.$errorElement.html(n.error);this.$field.addClass("analytics-error")}else{this.$field.removeClass("analytics-error");var r=AnalyticsUtils.getColumns(n);$.each(r,function(t,n){e.addColumn(n.type,n.name)});var i=AnalyticsUtils.getRows(n);e.addRows(i);t={colors:["#058DC7"],backgroundColor:"#fdfdfd",areaOpacity:.1,pointSize:8,lineWidth:4,legend:!1,hAxis:{textStyle:{color:"#888"},baselineColor:"#fdfdfd",gridlines:{color:"none"}},series:{0:{targetAxisIndex:0},1:{targetAxisIndex:1}},vAxes:[{textStyle:{color:"#888"},format:"#",textPosition:"in",baselineColor:"#eee",gridlines:{color:"#eee"}},{textStyle:{color:"#888"},format:"#",textPosition:"in",baselineColor:"#eee",gridlines:{color:"#eee"}}],chartArea:{top:10,bottom:10,width:"100%",height:"80%"}};this.$chart=new google.visualization.AreaChart(this.$chartElement.get(0));typeof this.$chart!="undefined"&&this.$chart.draw(e,t);var s=this;$(window).resize(function(){s.$chart.draw(e,t)})}},this))}});