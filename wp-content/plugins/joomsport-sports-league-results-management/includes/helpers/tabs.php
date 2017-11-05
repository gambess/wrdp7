<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class EsTabs
{
    public $tabclass = 'joomsporttab';
    public $count = 0;
    public function __construct($class = 'joomsporttab')
    {
        $this->tabclass = $class;
        ?>
		<script type="text/javascript">
		//<![CDATA[ 
			if(document.getElementsByClassName == undefined) { 
				   document.getElementsByClassName = function(cl) { 
				      var retnode = []; 
				      var myclass = new RegExp('\\b'+cl+'\\b'); 
				      var elem = this.getElementsByTagName('*'); 
				      for (var i = 0; i < elem.length; i++) { 
					 var classes = elem[i].className; 
					 if (myclass.test(classes)) { 
					    retnode.push(elem[i]); 
					 } 
				      } 
				      return retnode; 
				   } 
				}; 
			function show_etabs(tab_id){
				
				var tabz = document.getElementsByClassName('<?php echo $this->tabclass?>');

				for(i=0;i<tabz.length;i++) {
					var div_id = tabz[i].id+'_div';
  
					//jQuery('#'+div_id).get(0).style.display='none';
					removejsClass(tabz[i],'active');
                                        
                                        jQuery('#'+div_id).addClass('visuallyhidden');
				}
				

				addjsClass(document.getElementById(tab_id),"active");
				//getObj('jscurtab').value = tab_id;
				jQuery('#'+tab_id+'_div').removeClass('visuallyhidden');
			}
			function removejsClass(ele,cls) {
				if (hasjsClass(ele,cls)) {
					var reg = new RegExp('(\\s|^)'+cls+'(\\s|$)');
					ele.className=ele.className.replace(reg,'');
				}
			}
			function addjsClass(ele,cls) {
				if (!this.hasjsClass(ele,cls)) ele.className += " "+cls;
			}
			function hasjsClass(ele,cls) {
				return ele.className.match(new RegExp('(\\s|^)'+cls+'(\\s|$)'));
			}
			//]]> 
		</script>
		<?php

    }
    public function newTab($name, $tab_id, $bg = 'tab_star', $status = 'hide', $visible = true)
    {
        $bgst = '';
        $class = ($status == 'vis') ? 'active' : '';
        ++$this->count;

        return '<li id="'.$tab_id.'" class="'.$this->tabclass.' '.$class.'"><a href="javascript:show_etabs(\''.$tab_id.'\')"><span>'.$name.'</span></a></li>';
    }
}
?>