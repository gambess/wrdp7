<script type="text/javascript">
function change_num(item) {
   var s = document.getElementById('fpm_num');
   var t = document.getElementById('truncate');

   switch(item.value) {	
   case 'none':
     t.style.display = 'none';
     break;
   case 'paragraph':
     s.innerHTML= "<strong># paragraphs before cutoff</strong> (default <em>1</em>)";
     t.style.display = 'block';
     break;
   case 'letter': 
     s.innerHTML= "<strong># characters before cutoff</strong> (default <em>600</em>)";
     t.style.display = 'block';
     break;
   case 'word':
     s.innerHTML= "<strong># words before cutoff</strong> (default <em>200</em>)";
     t.style.display = 'block';
     break;
   }
}

function toggle_boxes(id) {
  var inputlist = document.getElementsByTagName("input");
  for (i = 0; i < inputlist.length; i++) {
    //alert(inputlist[i].getAttribute("name").replace('[]', ''));
    if ( inputlist[i].getAttribute("type") == 'checkbox' && 
	 inputlist[i].getAttribute("name").replace('[]', '') == id) {
      if (inputlist[i].checked) inputlist[i].checked = false
      else inputlist[i].checked = true;
    }
  }
}
</script>
