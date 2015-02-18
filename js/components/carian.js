$(function(){
	$('#btnCarian').click(function(){
		var carianStr = $('#carian').val();
		if(typeof carianStr !== 'undefined' && carianStr.length >= 2)
		$.ajax({
			type: "POST",
			url: pathAPI + "carian_tafsiran",
			data: {
				carian: carianStr
			},
			dataType: "json",
			success: function(res){
				if(res.success == true){
					var li_list = '';
					$.each(res.ayat, function(i, item){
						li_list += '<li>';
						li_list += '<strong>' + item.surah_nama + ', Ayat ' + item.ayat_no + '</strong><br />'
						li_list += item.ayat.replace(carianStr, '<b>' + carianStr + '</b>'); ;
						li_list += '</li>';
					});
					$('#senarai').html(li_list);
				}
			},
			error: function(){
				
			}
		});
	});
});