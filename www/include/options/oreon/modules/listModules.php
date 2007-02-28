<?
/**
Oreon is developped with GPL Licence 2.0 :
http://www.gnu.org/licenses/gpl.txt
Developped by : Julien Mathis - Romain Le Merlus

The Software is provided to you AS IS and WITH ALL FAULTS.
OREON makes no representation and gives no warranty whatsoever,
whether express or implied, and without limitation, with regard to the quality,
safety, contents, performance, merchantability, non-infringement or suitability for
any particular or intended purpose of the Software found on the OREON web site.
In no event will OREON be liable for any direct, indirect, punitive, special,
incidental or consequential damages however they may arise and even if OREON has
been previously advised of the possibility of such damages.

For information : contact@oreon-project.org
*/
	if (!isset($oreon))
		exit();	
		
	# Smarty template Init
	$tpl = new Smarty();
	$tpl = initSmartyTpl($path, $tpl);

	# start header menu
	$tpl->assign("headerMenu_name", $lang["mod_menu_module_name"]);
	$tpl->assign("headerMenu_rname", $lang["mod_menu_module_rname"]);
	$tpl->assign("headerMenu_release", $lang["mod_menu_module_release"]);
	$tpl->assign("headerMenu_author", $lang["mod_menu_module_author"]);
	$tpl->assign("headerMenu_isinstalled", $lang["mod_menu_module_is_installed"]);
	$tpl->assign("headerMenu_action", $lang["mod_menu_listAction"]);
	# end header menu
	
	#Different style between each lines
	$style = "one";
	# Get Modules
	$handle = opendir("./modules");
	#Fill a tab with a mutlidimensionnal Array we put in $tpl
	$elemArr = array();
	while (false !== ($filename = readdir($handle)))	{
		if ($filename != "." && $filename != "..")	{
			$moduleinfo = getModuleInfoInDB($filename, NULL);
			# Package already installed
			if (isset($moduleinfo["rname"]))	{				
				$elemArr[$i] = array("MenuClass"=>"list_".$style, 
						"RowMenu_name"=>$moduleinfo["name"],
						"RowMenu_rname"=>$moduleinfo["rname"],
						"RowMenu_release"=>$moduleinfo["release"],
						"RowMenu_author"=>$moduleinfo["author"],
						"RowMenu_isinstalled"=>$lang["yes"],
						"RowMenu_link"=>"?p=".$p."&o=w&id=".$moduleinfo["id"],
						"RowMenu_link_install"=>NULL,
						"RowMenu_link_delete"=>"?p=".$p."&o=w&id=".$moduleinfo["id"]."&o=d",
						"RowMenu_link_upgrade"=>"?p=".$p."&o=w&id=".$moduleinfo["id"]."&o=u");
				$style != "two" ? $style = "two" : $style = "one";
				$i++;
			}
			else	{
				# Valid package to install
				if (is_file("./modules/".$filename."/conf.php")) {
					include_once("./modules/".$filename."/conf.php");
					if (isset($module_conf[$filename]["name"]))	{							
						$elemArr[$i] = array("MenuClass"=>"list_".$style, 
								"RowMenu_name"=>$module_conf[$filename]["name"],
								"RowMenu_rname"=>$module_conf[$filename]["rname"],
								"RowMenu_release"=>$module_conf[$filename]["release"],
								"RowMenu_author"=>$module_conf[$filename]["author"],
								"RowMenu_isinstalled"=>$lang["no"],
								"RowMenu_link"=>"?p=".$p."&o=w&name=".$module_conf[$filename]["name"],
								"RowMenu_link_install"=>"?p=".$p."&o=w&name=".$module_conf[$filename]["name"]."&o=i",
								"RowMenu_link_delete"=>NULL,
								"RowMenu_link_upgrade"=>NULL);
						$style != "two" ? $style = "two" : $style = "one";
						$i++;
					}
				}
				# Non valid package
				else	{							
					$elemArr[$i] = array("MenuClass"=>"list_".$style, 
							"RowMenu_name"=>$filename,
							"RowMenu_rname"=>$lang["mod_menu_module_invalid"],
							"RowMenu_release"=>$lang["mod_menu_module_invalid"],
							"RowMenu_author"=>$lang["mod_menu_module_invalid"],
							"RowMenu_isinstalled"=>$lang["mod_menu_module_impossible"],
							"RowMenu_link"=>NULL);
					$style != "two" ? $style = "two" : $style = "one";
					$i++;
				}
			}
		}
	}
	closedir($handle);
	$tpl->assign("elemArr", $elemArr);
	$tpl->assign("action_install", $lang["mod_menu_listAction_install"]);
	$tpl->assign("action_delete", $lang["mod_menu_listAction_del"]);
	$tpl->assign("action_upgrade", $lang["mod_menu_listAction_upgrade"]);
	#
	##Apply a template definition
	#
	$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($tpl);
	$tpl->display("listModules.ihtml");
?>