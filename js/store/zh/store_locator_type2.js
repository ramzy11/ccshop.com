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
	
function createContent(cid,id)
{
	if(cid == "") return;

	var content = "";

    content += "<div class='store_country'>"+ country[cid] +"</div>\n";

    for(var type in store)
    {
        var data = store[type][cid];

        if(!data) continue;

        jQuery('#'+id).hide();

        content += "<div class='store_type'>"+display(type,'toupper')+"</div>\n";
        content += "<div class='store_cs'>"+data['customer_services'][0]+":"+data['customer_services'][1]+"</div>\n";

        content += "<div class='store_info_box'>\n";
        content += "<div class='store_title_line'><div class='store_title colfirst'>店鋪</div><div class='store_title colmiddle'>地址</div><div class='store_title collast'>電話</div><div class='store_title colmobfirst'>店鋪</div><div class='store_title colmoblast'>地址</div></div>\n";
		for(var i=0; i < data['store'].length; i++)
		{
			list = data['store'][i];
			key = list['type'];
			slist = list['shops'];
			content += "<hr/>\n";
			
			for(var idx = 0;idx < slist.length; idx++)
			{
				content += "<div class='store_content_line'><div class='store_content colfirst'>"+(idx == 0?display(key,''):'&nbsp;')+"</div><div class='store_content colmiddle'>"+slist[idx][0]+"</div><div class='store_content collast'>"+slist[idx][1]+"</div><div class='store_content colmobfirst'>"+(idx == 0?display(key,''):'&nbsp;')+"</div><div class='store_content colmoblast'>"+slist[idx][0]+"<br/>"+slist[idx][1]+"</div></div>\n";
			}
		}
        content += "</div>\n";
    }



    jQuery('#'+id).html(content);
    jQuery('#'+id).fadeIn(1000);
}


jQuery(document).ready(function(){
	 var sclist = jQuery('#store_country');
	 sclist.append("<option value=''>請選擇</option>");
	 for(var cid in country)
	 {
		sclist.append("<option value='" + cid +"'>" + country[cid] + "</option>");
	 }
	 
	 jQuery('#store_country').val('Hong_Kong');
	 createContent('Hong_Kong', 'store_content');
});
-->