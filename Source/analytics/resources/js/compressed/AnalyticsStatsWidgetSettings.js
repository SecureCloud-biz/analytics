$(document).ready(function(){$("#main #content form").submit(function(t){$("input[type=text], textarea, select",this).filter(":hidden").remove()})});