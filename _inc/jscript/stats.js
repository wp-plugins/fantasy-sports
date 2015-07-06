var lastTab=0;
jQuery.stats =
{
	init : function(pools, jpos, jrounds)
	{
		this.pools		= jQuery.parseJSON(pools);
		this.pos		= jQuery.parseJSON(jpos);
		this.rounds	= jQuery.parseJSON(jrounds);
		this.ps		= null;//	player stats
		this.scats		= null;
		this.total		= 0;
		this.catlength	= 0;
		
		this.mapPoolSport=[];
		for (var i in this.pools){
			if(!this.mapPoolSport[this.pools[i].organization])
				this.mapPoolSport[this.pools[i].organization]=[];
			this.mapPoolSport[this.pools[i].organization][i]=1;
		}
		
		this.mapPosSport=[];
		for (var i in this.pos){
			if(!this.mapPosSport[this.pos[i].org_id])
				this.mapPosSport[this.pos[i].org_id]=[];
			this.mapPosSport[this.pos[i].org_id][i]=1;
		}
		
		//event for POOL
		jQuery("#psb").change(function()
		{
			var poolID=jQuery(this).val(), html='', pools=jQuery.stats.pools, rounds=jQuery.stats.rounds, selected=' selected';
			
			if( parseInt(jQuery('option:selected', this).data("isround")) ){
				for(var i in rounds){
					if(rounds[i].poolID==poolID){
						html+='<option value="'+rounds[i].id+'"'+a+'>'+rounds[i].name+'</option>';
					}
					a='';
				}
				
				jQuery("#rndsb").html(html);
			}

			//reset pagination
			jQuery.stats.setPage(1);
			//action
			jQuery.stats.getStat();
		});
		
		//round selectbox
		jQuery("#rndsb").change(function()
		{
			//reset pagination
			jQuery.stats.setPage(1);
			//action
			jQuery.stats.getStat();
		});
		
		//auto select 1st item
		jQuery("#sports").prop("selectedIndex", 0);
		jQuery("#sports").trigger("change");
	},

	loadStats: function(data)
	{
		var html='', mk=count=0, fpos=jQuery("#posid").val(),fround=jQuery("#rndsb");
		
		if(data){//0: stats, 1: scorecats, 2: total results
			var ps=data[0], tmp=data[1], total=data[2], cats=[];
			this.ps=ps;
			this.total=total;
			this.catlength=tmp.length;
			
			for(var i in tmp){
				cats[tmp[i].id]=tmp[i];
			}
			this.scats=cats;
		}
		else{
			ps	= this.ps;
			cats	= this.scats;
			total	= this.total;
		}
		
		if(!ps.length){
			jQuery("#tbl tbody").html('<tr><td><b>No data</b></td></tr>');
			return;
		}
		//render score categories
		html='<tr><th>Player</th><th title="Position" class="pos" style="width:55px">Pos</th>';
		if(ps[0].team)
			html+='<th class="team" style="width:100px">Team</th><th  style="width:55px" class="minutes" title="Played time, in minutes">Mins</th>';
		
		for(var i in cats){
			if(cats[i].name.length>2)
				html+='<th title="'+cats[i].name+'">'+cats[i].name+'</th>';
			else
				html+='<th title="'+cats[i].name+'">'+cats[i].name+'</th>';
		}
		html+='</tr>';
		jQuery("#tbl thead").html(html);
		
		//render stats
		html='';
		for(var i in ps)
		{
			var stat=ps[i];
			//filter by position
			if(fpos!=0 && stat.posid != fpos){
				continue;
			}
			
			var o=jQuery.parseJSON(stat.scoring);
			//filter by round
			if(fround.attr('disabled')==undefined && fround.val()){
				o=o[fround.val()];
			}
			
			html+='<tr class="tr'+mk+'"><td>' +stat.name+ '</td><td>' +this.mapPos[stat.posid]+ '</td>';
			
			if(stat.team)
				html+='<td>' +stat.team+ '</td><td>' +stat.playedtime+ '</td>';
			//fill cells if empty
			if(!o){
				o=[];
				for(var j=this.catlength; j ; j--){
					o.push("");
				}
			}
			//match scores with cats
			for(var j in cats)
			{
				flg=0;
				for(var k in o)
				{
					if(o[k].scoring_category_id == j)
					{
						flg=o[k].points;break;
					}
				}
				
				html+='<td>'+flg+'</td>';
			}
			html+='</tr>';
			
			mk=1-mk;
			count++;
		}
		jQuery("#tbl tbody").html(html);
		
		//set pagination
		html='';
		pgmax=Math.ceil(total/20);
		var active=parseInt(jQuery("#pgv").val());
		
		for(var i=1; i <= pgmax; i++){
			html+='<div class="dib' +(active == i ? ' active' : '')+ '">' +i+ '</div>';
		}
		jQuery("#pg").html(html);
		jQuery("#pg .dib").click(function(){
			var page=parseInt(jQuery(this).text());
			
			jQuery.stats.setPage(page);
			//action
			jQuery.stats.getStat();
		});
		//
	},

	loadPools: function(org_id, is_round)
	{
		var html='', pools = this.pools, map=this.mapPoolSport[org_id];
		//toggle is_round filter
		if(is_round==1){
			jQuery("#rndsb")[0].disabled = false;
			jQuery("#rndsb").parents(".dib").css("display", "inline-block");
		}
		else{
			jQuery("#rndsb")[0].disabled = true;
			jQuery("#rndsb").parents(".dib").hide();
		}
		//render pools
		for(var i in map){
				html+='<option value="' +pools[i].poolID+ '" data-isround="' +is_round+ '">' +pools[i].poolName+ '</option>';
				flg=0;
		}
		//toggle UI when the sport has no pool
		if(!map){
			jQuery("#hidbox").hide();
			jQuery("#emptybox").show();
			return;
		}else{
			jQuery("#psb").html(html);
			jQuery("#emptybox").hide();
			jQuery("#hidbox").show();
		}
		//reset tab
		jQuery("#posid").val(0);
		//auto select 1st item
		jQuery("#psb").prop("selectedIndex", 0);
		jQuery("#psb").trigger("change");
		//clean pagination
		jQuery("#pg").html('');
		
		this.loadPositions(org_id);
	},

	loadPositions: function(org_id)
	{
		var positions = this.pos, mapPos=[], map=this.mapPosSport[org_id], html='<div class="dib tab-btn active" data-pos="0">All</div>';
		
		for(var i in map){
			html+='<div class="dib tab-btn" data-pos="'+positions[i].id+'">'+positions[i].name+'</div>';
			mapPos[positions[i].id]=positions[i].name;
		}
		this.mapPos=mapPos;//	prepare for loadStats
		jQuery("#postab").html(html);
		
		lastTab=jQuery(".tab-btn").first();
		jQuery(".tab-btn").click(function()
		{
				jQuery(this).addClass('active');
				
				if(lastTab[0] != jQuery(this)[0])
					lastTab.removeClass('active');
				else
					return false;

				jQuery("#posid").val(jQuery(this).data('pos'));
				lastTab=jQuery(this);
				
				//reset pagination
				jQuery.stats.setPage(1);
				//action
				jQuery.stats.getStat();
			});
	},
	
	setPage: function(page)
	{
		jQuery("#pg .dib").removeClass("active");
		jQuery("#pg .dib").eq(page-1).addClass("active");
		
		jQuery("#pgv").val(page);
	},
	
	getStat: function()
	{
		//clean columns
		jQuery("#tbl thead").html('');
		//loading indicator
		jQuery("#tbl tbody").html('<tr><td><b>Loading...</b></td></tr>');
		//clean pagination
		jQuery("#pg").html('');
		
		jQuery.post(ajaxurl, 'action=getStat&' + jQuery('#fstat').serialize(), function(result)
		{
			if(!result)
				{jQuery("#tbl tbody").html('<tr><td><b>No responding from server</b></td></tr>');return;}
			
			result = jQuery.parseJSON(result);
			if(result.result == 0)
			{
				jQuery('.public_message').html(result.msg).show();
			}
			else
			{
				var tmp=result.data;
				jQuery.stats.loadStats(tmp);
			}
		})
	}
}