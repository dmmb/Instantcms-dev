function toggleMembers(){
	var clubtype = $('select[name=clubtype]').val();
	if (clubtype == 'public') { 
		$('#nomembers').show(); $('#members').hide();  $('#minkarma').show(); 
	} else { 
		$('#nomembers').hide(); $('#members').show();  $('#minkarma').hide(); 
	}
}

$().ready(function() {  
  $('#moderator_remove').click(function() {

        var user = new Array;

        $('#moderslist option:selected').each(function () {
            user.push(this);
        });

        while (user.length){
            opt     = user.pop();
            opt2    = $(opt).clone();
            $(opt).remove().appendTo('#userslist1');
            $(opt2).remove();
        }

  });  
  $('#moderator_add').click(function() {

        var user_id = new Array;

        $('#userslist1 option:selected').each(function () {
            user_id.push(this.value);
        });

   		$('#userslist1 option:selected').remove().appendTo('#moderslist');

        while (user_id.length){
            id = user_id.pop();
            $('#userslist2 option[value='+id+']').remove();
        }

  });  
 
  $('#member_remove').click(function() {
        var user = new Array;

        $('#memberslist option:selected').each(function () {
            user.push(this);
        });

        var user_id = new Array;

        $('#memberslist option:selected').each(function () {
            user_id.push(this.value);
        });

        while (user.length){
            opt     = user.pop();
            opt2    = $(opt).clone();
            $(opt).remove().appendTo('#userslist1');
            $(opt2).remove().appendTo('#userslist2');
        }

        while (user_id.length){
            id = user_id.pop();
            $('#moderslist option[value='+id+']').remove();
        }

  });

  $('#member_add').click(function() {
      
        var user_id = new Array;

        $('#userslist2 option:selected').each(function () {
            user_id.push(this.value);
        });

   		$('#userslist2 option:selected').remove().appendTo('#memberslist');       

  });  
 
  $("#addform").submit(function() { 
		$('#moderslist').each(function(){
			$('#moderslist option').attr("selected","selected");
		});  
		$('#memberslist').each(function(){
			$('#memberslist option').attr("selected","selected");
		});  
  });
  
});  
