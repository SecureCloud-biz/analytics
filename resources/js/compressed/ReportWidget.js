Analytics.ReportWidget=Garnish.Base.extend({report:null,$title:null,$body:null,$date:null,$spinner:null,$error:null,$report:null,init:function(e,t){this.$element=$("#"+e),this.$title=$(".title",this.$element),this.$body=$(".body",this.$element),this.$date=$(".date",this.$element),this.$spinner=$(".spinner",this.$element),this.$spinner.removeClass("body-loading"),this.$error=$(".error",this.$element);var s;if("undefined"!=typeof t.request&&(s=t.request),"undefined"!=typeof t.cachedResponse&&t.cachedResponse){this.$spinner.removeClass("hidden");var r=t.cachedResponse;this.parseResponse(r)}else s&&this.sendRequest(s)},sendRequest:function(e){this.$spinner.removeClass("hidden"),$(".report",this.$body).remove(),this.$error.addClass("hidden"),Craft.postActionRequest("analytics/reports/getReport",e,$.proxy(function(e,t){if("success"==t&&"undefined"==typeof e.error)this.parseResponse(e);else{var s="An unknown error occured.";"undefined"!=typeof e&&e&&"undefined"!=typeof e.error&&(s=e.error),this.$error.html(s),this.$error.removeClass("hidden")}window.dashboard.grid.refreshCols(!0)},this))},parseResponse:function(e){var t=e,s=e.metric,r=e.periodLabel,n=e.type,i=n.charAt(0).toUpperCase()+n.slice(1);this.$report=$('<div class="report"></div>'),this.$report.appendTo(this.$body),this.$title.html(s),this.$date.html(r),t.onAfterDraw=$.proxy(function(){this.$spinner.addClass("hidden")},this),this.report=new Analytics.reports[i](this.$report,t)}});