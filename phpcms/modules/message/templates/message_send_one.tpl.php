<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header','admin');
?>
	<link href="<?php echo CSS_PATH?>dialog.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="<?php echo CSS_PATH?>zTreeStyle/demo.css" type="text/css">
	<link rel="stylesheet" href="<?php echo CSS_PATH?>zTreeStyle/zTreeStyle.css" type="text/css">
	<script type="text/javascript" src="<?php echo JS_PATH?>jquery-1.4.4.min.js"></script>
	<script type="text/javascript" src="<?php echo JS_PATH?>jquery.ztree.core-3.5.js"></script>
	<script type="text/javascript" src="<?php echo JS_PATH?>jquery.ztree.excheck-3.5.js"></script>
	
	<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>dialog.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>content_addtop.js"></script>
	<script type="text/javascript" src="<?php echo JS_PATH?>swf2ckeditor.js"></script>

	<SCRIPT type="text/javascript">
		<!--
		var setting = {
			check: {
				enable: true,
				chkboxType: {"Y":"", "N":""}
			},
			view: {
				dblClickExpand: false
			},
			data: {
				simpleData: {
					enable: true
				}
			},
			callback: {
				beforeClick: beforeClick,
				onCheck: onCheck
			}
		};

		var zNodes =[

			<?php 
       $data = bizorder_show_linkage(3376, '', $province, 0);
		    if (is_array($data) && !empty($data)) {
					foreach ($data as $d) {
						//$content=$content."<option value='".$d['linkageid']."'>".$d['title']."</option>";
						echo "{id:".$d['linkageid'].", pId:0, name:'".$d['title']."', open:true, nocheck:true},";
						//echo $d['title'];
						//echo $d['linkageid'];
						$sql = "SELECT * from v9_member,v9_member_detail WHERE v9_member_detail.dept='".$d['linkageid']."' AND v9_member_detail.userid=v9_member.userid";
						$this->db->query($sql);			
						$data = $this->db->fetch_array($sql);
									foreach($data as $r) {
										echo "{id: ".$r['userid'].", pId:".$d['linkageid'].", name:'".$r['nickname']."'},";
										//echo $r['userid'];
										//echo $r['nickname'];
									}						
					}
				}
				?>

		 ];

		function beforeClick(treeId, treeNode) {
			var zTree = $.fn.zTree.getZTreeObj("treeDemo");
			zTree.checkNode(treeNode, !treeNode.checked, null, true);
			return false;
		}
		
		function onCheck(e, treeId, treeNode) {
			var zTree = $.fn.zTree.getZTreeObj("treeDemo"),
			nodes = zTree.getCheckedNodes(true),
			v = "";
			n = "";
			for (var i=0, l=nodes.length; i<l; i++) {
				v += nodes[i].name + ",";
				n += nodes[i].id + ",";
			}
			if (v.length > 0 ) v = v.substring(0, v.length-1);
			var cityObj = $("#citySel");
			var cityObj1 = $("#tousername");
			cityObj.attr("value", v);
			cityObj1.attr("value", n);
		}

		function showMenu() {
			var cityObj = $("#citySel");
			var cityOffset = $("#citySel").offset();
			$("#menuContent").css({left:cityOffset.left + "px", top:cityOffset.top + cityObj.outerHeight() + "px"}).slideDown("fast");

			$("body").bind("mousedown", onBodyDown);
		}
		function hideMenu() {
			$("#menuContent").fadeOut("fast");
			$("body").unbind("mousedown", onBodyDown);
		}
		function onBodyDown(event) {
			if (!(event.target.id == "menuBtn" || event.target.id == "citySel" || event.target.id == "menuContent" || $(event.target).parents("#menuContent").length>0)) {
				hideMenu();
			}
		}

		$(document).ready(function(){
			$.fn.zTree.init($("#treeDemo"), setting, zNodes);
		});
		//-->
	</SCRIPT>
<script type="text/javascript"> 
<!--
$(function(){
	$.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});
	$("#subject").formValidator({onshow:"<?php echo L('input','','admin').L('subject')?>",onfocus:"<?php echo L('subject').L('no_empty')?>"}).inputValidator({min:1,max:999,onerror:"<?php echo L('subject').L('no_empty')?>"});
	$("#content").formValidator({onshow:"<?php echo L('content').L('no_empty')?>",onfocus:"<?php echo L('content').L('no_empty')?>"}).inputValidator({min:1,max:999,onerror:"<?php echo L('content').L('no_empty')?>"});
  //$("#tousername").formValidator({onshow:"<?php echo L('input','','admin').L('touserid')?>",onfocus:"<?php echo L('touserid').L('no_empty')?>"}).inputValidator({min:1,onerror:"<?php echo L('input','','admin').L('touserid')?>"}).ajaxValidator({type : "get",url : "",data :"m=message&c=message&a=public_name",datatype : "html",async:'true',success : function(data){if( data == 1 ){return true;}else{return false;}},buttons: $("#dosubmit"),onerror : "<?php echo L('not_myself')?>! ",onwait : "<?php echo L('connecting')?>"});
})

//-->
</script> 

<div id="menuContent" class="menuContent" style="display:none; position: absolute;z-index:100;">
	<ul id="treeDemo" class="ztree" style="margin-top:0; width:180px; height: 300px;"></ul>
</div>

<div class="pad-lr-10">


				<form action="?m=message&c=message&a=send_one" method="post" name="myform" id="myform">
				<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

					<tr>
						<th width="100"><?php echo L('subject')?>：</th>
						<td><input type="text" name="info[subject]" id="subject"	size="30" class="input-text"></td>
					</tr>
					
					
					<tr>
						<th width="100"><?php echo L('touserid')?>：</th>
						<td>
							<input id="citySel" type="text" readonly value="" style="width:500px;" onclick="showMenu();" />
							<input name="info[send_to_id]" id="tousername" type="text" readonly value="" style="width:500px;" />
							&nbsp;<a id="menuBtn" href="#" onclick="showMenu(); return false;">select</a>&nbsp;&nbsp;<span class="highlight_red">勾选 checkbox 或者 点击节点 进行选择</span>
						</td>
					</tr>

					<tr>
						<th><strong><?php echo L('content')?>：</strong></th>
						<td><textarea name="info[content]" id="content"></textarea><?php echo form::editor('content');?></td>
					</tr>
					<tr>
						
					<?php $authkey = upload_key('10,rar|zip|html|htm|doc|docx|pdf,1');?>      
					<tr>
						<th width="100"> 附件上传：</th> 
						<td><input name="info[downfiles]" type="hidden" value="1">
						<fieldset class="blue pad-10">
        		<legend>文件列表</legend><ul id="downfiles" class="picList"></ul>
						</fieldset>
						<div class="bk10"></div>
						<input type="button"  class="button" value="多文件上传" onclick="javascript:flashupload('downfiles_multifile', '附件上传','downfiles',change_multifile,'10,rar|zip|html|htm|doc|docx|pdf,1','message','','<?php echo $authkey ?>')"/>    <input type="button" class="button" value="添加远程地址" onclick="add_multifile('downfiles')"></td>
					</tr>
						
						<th></th>
						<td><input
						type="submit" name="dosubmit" id="dosubmit" class="button"
						value=" <?php echo L('submit')?> "></td>
					</tr>
				
				</table>
				</form>
</div>
</body>
</html>
