$(function(){			
	senarai_surah();
	
	$('#carian').on('keyup', function(data){
		if($(this).val().length > 1){
			senarai_surah($(this).val());
		} else if ($(this).val().length > 0){
			senarai_surah();
		}
	});
});

function senarai_surah(val = ''){
	$.ajax({
		type: "POST",
		url: pathAPI + "senarai_surah",
		data: {
			surah: val
		},
		dataType: "json",
		success: function(res){
			if(res.success == true){
				var li_list = '';
				$.each(res.surah, function(i, item){
					li_list += '<li><a href="surah.html?yourID=' + item.surah_id + '">' + item.surah_nama + '<a/></li>';
				});
				$('#senarai').html(li_list);
			}
		}
	});
}