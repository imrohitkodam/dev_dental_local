<?php
/**
 * @version     1.0.0
 * @package     com_tjlms
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      TechJoomla <extensions@techjoomla.com> - www.techjoomla.com
 */

// no direct access
defined('_JEXEC') or die;

JHTML::_('behavior.tooltip');
JHTML::_('behavior.framework');
JHTML::_('behavior.modal');

$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root(true).'/administrator/components/com_tjlms/assets/css/morris.css');

$this->currentVersion = 1.0;
// Load jQuery.
if (JVERSION >= '3.0')
{
	JHtml::_('jquery.framework');
}

$document->addScript(JUri::root(true).'/administrator/components/com_tjlms/assets/js/raphael.min.js');
$document->addScript(JUri::root(true).'/administrator/components/com_tjlms/assets/js/morris.min.js');

//$helperobj = new comquick2cartHelper;

if (JVERSION < '3.0')
{
	$strapperClass = 'techjoomla-bootstrap';
}
else
{
	$strapperClass = '';
}

// Global icon constants.
if (JVERSION >= '3.0')
{
	define('LMS_DASHBORD_ICON_ORDERS', "icon-credit");
	define('LMS_DASHBORD_ICON_ITEMS', "icon-bars");
	define('LMS_DASHBORD_ICON_SALES', "icon-chart");
	define('LMS_DASHBORD_ICON_AVG_ORDER', "icon-credit");
	define('LMS_DASHBORD_ICON_ALL_SALES', "icon-chart");
	define('LMS_DASHBORD_ICON_USERS', "icon-users");
	define('LMS_DASHBORD_ICON_COURSE', "icon-book");
	define('LMS_DASHBORD_ICON_COURSE_COMPLETE', "icon-ok");
	define('LMS_DASHBORD_ICON_REVENUE', "icon-briefcase");
	define('TJ_ICON_THUMB_UP', "icon-thumbs-up");
	define('TJ_ICON_THUMB_DOWN', "icon-thumbs-down");
	define('TJ_ICON_USERS', "icon-users");

}
else
{
	define('LMS_DASHBORD_ICON_ORDERS', "icon-shopping-cart");
	define('LMS_DASHBORD_ICON_ITEMS', "icon-gift");
	define('LMS_DASHBORD_ICON_SALES', "icon-briefcase");
	define('LMS_DASHBORD_ICON_AVG_ORDER', "icon-th-large");
	define('LMS_DASHBORD_ICON_ALL_SALES', "icon-briefcase");
	define('LMS_DASHBORD_ICON_USERS', "icon-user");
	define('LMS_DASHBORD_ICON_COURSE', "icon-book");
	define('LMS_DASHBORD_ICON_COURSE_COMPLETE', "icon-ok");
	define('LMS_DASHBORD_ICON_REVENUE', "icon-briefcase");
	define('TJ_ICON_THUMB_UP', "icon-thumbs-up");
	define('TJ_ICON_THUMB_DOWN', "icon-thumbs-down");
	define('TJ_ICON_USERS', "icon-users");
}

?>

<div class="<?php echo $strapperClass; ?> tj-dashboard">
	<div class="clearfix">&nbsp;</div>

	<!--HEADER TEACHER_DASHBOARD-->
	<div class="row-fluid page-header">
		<div class="span12">
			<!--DASHBOARD HEADING-->
			<h3><?php echo JText::sprintf('COM_TJLMS_STUD_REPORT_HEADING',$this->CourseName,$this->StudentName); ?></h3>
		</div>
	</div>
	<!--HEADER TEACHER_DASHBOARD ENDS-->

	<div class="row-fluid ">
		<div class="span12">
			<div class="span6">
				<div class="row-fluid ">
					<div class="span12">
						<!--DIV FOR GRAPH -->
						<div id="progress_graph" style="height: 250px;">
						</div>
					</div>
					<div class="span12" style="display:none;" >
						<div class="alert alert-info">
						<?php echo JText::sprintf('COM_TJLMS_STUD_REPORT_COURSE_SUBSLEFT',$this->left_subsdays); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="span6" style="display:none;">
				<!--DIV FOR ACTIVITY -->
				<h5><?php echo JText::_('COM_TJLMS_COURSE_ACTIVITY'); ?></h5>
				<div style="overflow:scroll; ">
					<div class="bs-docs-example"><!-- @TODO CHANGE the sample activity stream block -->
						<div class="media">
						  <a class="pull-left" href="#">
							<img class="media-object" data-src="holder.js/64x64" alt="64x64" style="width: 64px; height: 64px;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAACd0lEQVR4Xu2Y+4vpQRjGH5LdDVFai0jLJmklIZE/X4Rkw9au64ofKLlfcsued4rOObVnT/Ntz9l4p6SYeec7zzzzfBjVcDg84IKbigVgB/AR4Ay44AwEhyBTgCnAFGAKMAUuWAHGIGOQMcgYZAxeMAT4zxBjkDHIGGQMMgYZgxesAGNQKQbn8zleXl5A7zc3N/D7/TAYDL946u3tDfV6HaFQCGaz+VO/fUXNjyZV5IDtdot0Og2tVgu32y0WqVarEY/HT/ONx2MUCgUcDoe/EuArav5JcUUC9Pt9lMtlPD4+wmq1YrlcioXqdDox52azQTabxdXVFabT6UmAVquFRqMBj8cDp9OJTCYDlUqFWCyGH46UqvmprT7ooEiAo7Vvb28xmUxwfX0Nr9cLk8kkpisWi9jtdnC5XCiVSicBSCT6jtxBfUejESKRCIxGI2Rr/hcBjjtJAtzf36NarQoXJJNJdDodNJtNhMNh8VmlUkEgEAD1pWOyXq+FO8gldHzoRU1JTRkRFDmg1+uJhVHw2e12tNtt1Go1JBIJPD8/C1f83kiEu7s7LBYL5HI57Pd7IUowGBRdldT85wJQYKVSKZH+5ACyL+0sOWA2mwn7U6PzT2f+4eEBNpsNGo0G+Xxe7L7FYkG324XP54PD4YBsTcoZmabIATQh7fLr66vYUQq/nzPg+ECDwQBPT0+nDCBs0qKPR4KcsFqtEI1GodfrpWrKLJ7GKBZAduLvMo4FUPpL8LvspOxzsAPYAXwlxldifCUmm6DnMI4pwBRgCjAFmALnkOaya2AKMAWYAkwBpoBsgp7DOKYAU4ApwBRgCpxDmsuu4eIp8A4wEGCfEGZc+QAAAABJRU5ErkJggg==">
						  </a>
						  <div class="media-body">
							<h6 class="media-heading">Media heading</h6>
							Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo. Cras sit amet nibh libero, in gravida nulla.
						  </div>
						</div>
						<div class="media">
						  <a class="pull-left" href="#">
							<img class="media-object" data-src="holder.js/64x64" alt="64x64" style="width: 64px; height: 64px;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAACd0lEQVR4Xu2Y+4vpQRjGH5LdDVFai0jLJmklIZE/X4Rkw9au64ofKLlfcsued4rOObVnT/Ntz9l4p6SYeec7zzzzfBjVcDg84IKbigVgB/AR4Ay44AwEhyBTgCnAFGAKMAUuWAHGIGOQMcgYZAxeMAT4zxBjkDHIGGQMMgYZgxesAGNQKQbn8zleXl5A7zc3N/D7/TAYDL946u3tDfV6HaFQCGaz+VO/fUXNjyZV5IDtdot0Og2tVgu32y0WqVarEY/HT/ONx2MUCgUcDoe/EuArav5JcUUC9Pt9lMtlPD4+wmq1YrlcioXqdDox52azQTabxdXVFabT6UmAVquFRqMBj8cDp9OJTCYDlUqFWCyGH46UqvmprT7ooEiAo7Vvb28xmUxwfX0Nr9cLk8kkpisWi9jtdnC5XCiVSicBSCT6jtxBfUejESKRCIxGI2Rr/hcBjjtJAtzf36NarQoXJJNJdDodNJtNhMNh8VmlUkEgEAD1pWOyXq+FO8gldHzoRU1JTRkRFDmg1+uJhVHw2e12tNtt1Go1JBIJPD8/C1f83kiEu7s7LBYL5HI57Pd7IUowGBRdldT85wJQYKVSKZH+5ACyL+0sOWA2mwn7U6PzT2f+4eEBNpsNGo0G+Xxe7L7FYkG324XP54PD4YBsTcoZmabIATQh7fLr66vYUQq/nzPg+ECDwQBPT0+nDCBs0qKPR4KcsFqtEI1GodfrpWrKLJ7GKBZAduLvMo4FUPpL8LvspOxzsAPYAXwlxldifCUmm6DnMI4pwBRgCjAFmALnkOaya2AKMAWYAkwBpoBsgp7DOKYAU4ApwBRgCpxDmsuu4eIp8A4wEGCfEGZc+QAAAABJRU5ErkJggg==">
						  </a>
						  <div class="media-body">
							<h6 class="media-heading">Media heading</h6>
							Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo.

						  </div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div><!--DIV row-fluid ENDS-->

	<div class="row-fluid">
		<div class="span12">
			<h5><?php echo JText::_('COM_TJLMS_STUD_REPORT_COURSE_DETAILS'); ?></h5>
			<table class="table table-condensed table-striped">
				<thead>
					<tr>
						<th ><?php echo JText::_('COM_TJLMS_STUD_COURSE_LESSON'); ?></th>
						<th ><?php echo JText::_('COM_TJLMS_STUD_COURSE_ATTEMPT'); ?></th>
						<th ><?php echo JText::_('COM_TJLMS_STUD_COURSE_SCORERS'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($this->CourseDetails as $scorer ){ ?>
					<tr>
						<td> <?php echo $scorer->name; ?> </td>
						<td><?php echo $scorer->attempt; ?> </td>
						<td><?php echo $scorer->score; ?> </td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>


</div><!--BOOTSTRAP DIV-->

<?php
$incomedata[0] = '';
?>
<link rel="stylesheet" href="http://cdn.oesmith.co.uk/morris-0.4.3.min.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
 <script src="http://cdn.oesmith.co.uk/morris-0.4.3.min.js"></script>
<script>


// Morris Donut Chart
	Morris.Donut({
		element: 'progress_graph',
		data: [
			{label: 'Completed', value: <?php echo $this->Completed; ?> },
			{label: 'Pending', value: <?php echo $this->Pending; ?> }
		],
		colors: ["#a6e182", "orange", "#FF0000"],
		formatter: function (y) { return y + "%" }
	});


	/*
		 data: [
    { year: '04-01-2014', value: 20 },
    { year: '08-02-2014', value: 10 },
    { year: '14-02-2014', value: 5 },
    { year: '24-04-2014', value: 5 },
    { year: '14-05-2014', value: 15 }
  ],
	Morris.Bar({
		element: 'hero-bar',
		data: [
			{device: '1', sells: 136},
			{device: '3G', sells: 1037},
			{device: '3GS', sells: 275},
			{device: '4', sells: 380},
			{device: '4S', sells: 655},
			{device: '5', sells: 1571}
		],
		xkey: 'device',
		ykeys: ['sells'],
		labels: ['Sells'],
		barRatio: 0.4,
		xLabelMargin: 10,
		hideHover: 'auto',
		barColors: ["#3d88ba"]
	});

	// Morris Donut Chart
	Morris.Donut({
		element: 'hero-donut',
		data: [
			{label: 'Completed Orders', value: 55 },
			{label: 'Pending Orders', value: 25 },
			{label: 'Refunded Orders', value: 15 }
		],
		colors: ["#a6e182", "orange", "#FF0000"],
		formatter: function (y) { return y + "%" }
	});*/
</script>

<style type="text/css">

.tj-dashboard .no-margin-span
{
	margin-left: 2.5641025641026% !important;
}

.tj-dashboard .hr-margin
{
	margin:20px !important;
}

.tj-dashboard .navbar-inner {
	-moz-box-shadow: 0 0 0 0;
	-webkit-box-shadow: 0 0 0 0;
	box-shadow: 0 0 0 0;
}
.tj-dashboard .parent-statbox-value
{
	//position: relative;
	text-align:right;
}
.tj-dashboard .navbar-inner{
	background: hsl(0, 0%, 98%);
}
.tj-dashboard .statbox {
	background: #EEEEEE;
	//padding: 10px;
	position: relative;
	text-align:center;
}

.tj-dashboard .statbox .statbox-title {
	top: 12px;
	color: white;
	display: block;
	font-size: 13px;
	margin-top: 4px;
}

.tj-dashboard .statbox .statbox-value {
	font-size: 25px;
	//font-weight: bold;
	color: white;
	//position: absolute;
	top:30%;
	right:0;
}

.tj-dashboard .statbox .statbox-overlay
{
	-webkit-border-radius:2px;
	-moz-border-radius:2px;
	border-radius:2px;
	//width:84px;
	//padding:20px;
	padding:20px 4px 10px 10px;
	//text-align:center;
	//margin-right:10px;
	//float:left;
	overflow:hidden
}

.tj-dashboard .statbox .statbox-overlay.statbox-blue
{
	background:#36a9e1
}

.tj-dashboard .statbox .statbox-overlay.statbox-orange
{
	background:#FFA500
}

.tj-dashboard .statbox .statbox-overlay.statbox-lightBlue
{
	background:#67c2ef
}

.tj-dashboard .statbox .statbox-overlay.statbox-green
{
	background:#9abc32
}

.tj-dashboard .statbox .statbox-overlay.statbox-grey
{
	background:#808080
}

.tj-dashboard .statbox .statbox-overlay.statbox-darkGreen
{
	background:#00a489
}

.tj-dashboard .statbox .statbox-overlay.statbox-lightOrange
{
	background:#fabb3d
}

.tj-dashboard .statbox .statbox-overlay.statbox-yellow
{
	background:#e8b110
}
.tj-dashboard .statbox .statbox-overlay.statbox-brown
{
	background:#ab8465
}

.tj-dashboard .statbox .statbox-overlay.statbox-red
{
	background:#d53f40
}
.tj-dashboard .statbox .statbox-overlay.statbox-purple
{
	background:#847cc5
}




.tj-dashboard [class^="icon-"], [class*=" icon-"]{
	vertical-align:inherit;
}

.tj-dashboard .statbox-icons{
	font-size:25px; color:white;
	width:35px;
}

.tj-dashboard .collapse-icon {
	display: inline-block;
	width: 0;
	height: 0;
	vertical-align: top;
	border-top: 8px solid #000;
	border-right: 8px solid transparent;
	border-left: 8px solid transparent;
}


.tj-dashboard .stat-block {
	border: 1px solid #ccc;
	background: white;
	margin: 1em 0em;
	border-top: none;
}

.tj-dashboard .stat-block-content {
	margin: 1em;
	min-height: .25em;
}

.tj-dashboard .stat-block-header {
	margin-bottom: 0px;
	border-right: none;
	border-left: none;
	-webkit-border-radius: 0px;
	-moz-border-radius: 0px;
	border-radius: 0px;
}

.tj-dashboard .stat-block-header div {
	padding-top: 10px;
}

.tj-dashboard .stat-block-content {
	margin: 5px;
	min-height: 0px;
}

.inline-block-class
{
	display:inline-block;
}
.media .pull-left
{
	margin-right:10px !important;
}

.tj-dashboard .text_right-class
{
	text-align:right;
}

.tj-dashboard .tj_Nocolor{
background-color: inherit;
}
</style>
