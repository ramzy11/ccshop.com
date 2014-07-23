<!--
function display(str, toUpper)
{
		str = str.replace(/\_/g,' ');
		
		switch(toUpper)
		{
			case 'toupper':
			str = str.toUpperCase();
			break;
			
			case 'ucwords':
			str = str.replace(/^[a-z]/, function(m){ return m.toUpperCase()});
			str = str.replace(/\s[a-z]/,function(m) {return m.toUpperCase()});
			break;
			
			default:
			break;
		}
		return str;
}
	
function createContent(country,id)
{
	if(country == "") return;

	var content = "";
	
	var data = store[country];

	jQuery('#'+id).hide();
	
	content += "<div class='store_country'>"+display(country,'toupper')+"</div>\n";
	content += "<div class='store_cs'>Customer Service: "+data['customer_service']+"</div>\n";
	
	content += "<div class='store_info_box'>\n";
	content += "<div class='store_title_line'><div class='store_title colfirst'>STORE</div><div class='store_title colmiddle'>ADDRESS</div><div class='store_title collast'>TEL</div><div class='store_title colmobfirst'>STORE</div><div class='store_title colmoblast'>ADDRESS</div></div>\n";
	for(var key in data['store'])
	{
		slist = data['store'][key];
		
		content += "<hr/>\n";
		
		for(var idx = 0;idx < slist.length; idx++)
		{
			content += "<div class='store_content_line'><div class='store_content colfirst'>"+(idx == 0?display(key,''):'&nbsp;')+"</div><div class='store_content colmiddle'>"+slist[idx][0]+"</div><div class='store_content collast'>"+slist[idx][1]+"</div><div class='store_content colmobfirst'>"+(idx == 0?display(key,''):'&nbsp;')+"</div><div class='store_content colmoblast'>"+slist[idx][0]+"<br/>"+slist[idx][1]+"</div></div>\n";
		}
	}
	content += "</div>\n";
	
	jQuery('#'+id).html(content);
	jQuery('#'+id).fadeIn(1000);
}


jQuery(document).ready(function(){
	 var sclist = jQuery('#store_country');
	 sclist.append("<option value=''>Please Select</option>");
	 for(var i = 0; i < country.length;  i++)
	 {
		sclist.append("<option value='" + country[i]+ "'>" + display(country[i],'ucwords') + "</option>");
	 }
	 
	 jQuery('#store_country').val('Hong_Kong');
	 createContent('Hong_Kong', 'store_content');
});
-->