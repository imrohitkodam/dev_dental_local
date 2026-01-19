<?php 
/** 
 * @package JMAP::OVERVIEW::administrator::components::com_jmap
 * @subpackage views
 * @subpackage overview
 * @subpackage tmpl
 * @author Joomla! Extensions Store
 * @copyright (C) 2014 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
$rowSpacer = '<p></p>';
$reportDelimiter = '_________________';
?>
<!doctype html public "-//w3c//dtd html 3.2//en">

<html>
<head>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
</head>

<body text="#000000">
<b><font size="4" color="#0028D3"><?php echo $this->statsDomain; ?></font></b>
<?php 
echo $rowSpacer;
$arrayLabels = array (
	'AVERAGE' => '#f89406',
	'FAST' => '#3c763d',
	'NONE' => '#428bca',
	'SLOW' => '#d30000'
);

$successTest = '<font color="#3c763d">' . JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_SUCCESS_DESC') . '</font>';
$failedTest = '<font color="#d30000">' . JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_FAILURE_DESC') . '</font>';
$notavailableTest = '<font color="#428bca">' . JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_NOTAVAILABLE_DESC') . '</font>';
?>

<!-- PAGESPEED OVERVIEW REPORT BY API RESPONSE-->
<p></p>
<b><font size="3" color="#0028D3"><?php echo JText::_ ('COM_JMAP_GOOGLE_PAGESPEED_REPORT_SUMMARY' ) . $reportDelimiter;?></font></b>
<table>
	<thead>
		<tr>
			<th>
				<?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_SUMMARY_SCORE' ); ?>
			</th>
			<th>
				<?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_SUMMARY_SCORE_VOTE' ); ?>
			</th>
		</tr>
	</thead>
	
	<tbody>
		<?php 
			// Calculate the score category, range, colors for labels and sliders
			$absoluteScore = isset($this->googleData['lighthouseResult']['categories']['performance']) ? (int)($this->googleData['lighthouseResult']['categories']['performance']['score'] * 100) : -1;
			if($absoluteScore <= 49) {
				$summaryScoreVoteText = JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_SUMMARY_SCORE_SLOW');
				$summaryScoreVoteStyle = '#d30000';
			} elseif ($absoluteScore >= 50 && $absoluteScore <= 89) {
				$summaryScoreVoteText = JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_SUMMARY_SCORE_AVERAGE');
				$summaryScoreVoteStyle = '#f89406';
			} elseif ($absoluteScore >= 90 && $absoluteScore <= 100) {
				$summaryScoreVoteText = JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_SUMMARY_SCORE_FAST');
				$summaryScoreVoteStyle = '#3c763d';
			} else {
				$summaryScoreVoteText = JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_SUMMARY_SCORE_NONE');
				$summaryScoreVoteStyle = '#428bca';
			}
		?>
		<tr>
			<td>
				<font color="<?php echo $summaryScoreVoteStyle;?>"><b><?php echo $absoluteScore;?></b></font>
			</td>
			<td>
				<font color="<?php echo $summaryScoreVoteStyle;?>"><b><?php echo $summaryScoreVoteText;?></b></font>
			</td>
	</tbody>
</table>

<!-- PAGESPEED PERFORMANCE REPORT BY API RESPONSE-->
<p></p>
<b><font size="3" color="#0028D3"><?php echo JText::_ ('COM_JMAP_GOOGLE_PAGESPEED_PERFORMANCE_REPORT' ) . $reportDelimiter;?></font></b>
<table>
	<thead>
		<tr>
			<?php if(isset($this->googleData['loadingExperience']['metrics'])):?>
				<th>
					<?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_AVERAGE_FCP' ); ?>
				</th>
				<th>
					<?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_FID' ); ?>
				</th>
			<?php endif;?>
			<?php if(isset($this->googleData['lighthouseResult']['audits'])):?>
				<th>
					<?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_FCP' ); ?>
				</th>
				<th>
					<?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_SPEED_INDEX' ); ?>
				</th>
				<th>
					<?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_FMP' ); ?>
				</th>
				<th>
					<?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_FCI' ); ?>
				</th>
				<th>
					<?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_TTI' ); ?>
				</th>
				<th>
					<?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_MPFID' ); ?>
				</th>
				<th>
					<?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_JSEXEC' ); ?>
				</th>
			<?php endif;?>
		</tr>
	</thead>
	
	<tbody>
		<tr>
			<?php if(isset($this->googleData['loadingExperience']['metrics'])):?>
				<td>
					<font color="<?php echo $arrayLabels[$this->googleData['loadingExperience']['metrics']['FIRST_CONTENTFUL_PAINT_MS']['category']];?>"><?php echo number_format($this->googleData['loadingExperience']['metrics']['FIRST_CONTENTFUL_PAINT_MS']['percentile'] / 1000, 2); ?> s - <?php echo $this->googleData['loadingExperience']['metrics']['FIRST_CONTENTFUL_PAINT_MS']['category']; ?></font>
				</td>
				<td>
					<font color="<?php echo $arrayLabels[$this->googleData['loadingExperience']['metrics']['FIRST_INPUT_DELAY_MS']['category']];?>"><?php echo $this->googleData['loadingExperience']['metrics']['FIRST_INPUT_DELAY_MS']['percentile']; ?> ms - <?php echo $this->googleData['loadingExperience']['metrics']['FIRST_INPUT_DELAY_MS']['category']; ?></font>
				</td>
			<?php endif;?>
			<?php if(isset($this->googleData['lighthouseResult']['audits'])):?>
				<td>
					<span data-content="<?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_FCP_DESC');?>"><?php echo $this->googleData['lighthouseResult']['audits']['first-contentful-paint']['displayValue'];?></span>
				</td>
				<td>
					<span data-content="<?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_SPEED_INDEX_DESC');?>"><?php echo $this->googleData['lighthouseResult']['audits']['speed-index']['displayValue'];?></span>
				</td>
				<td>
					<span data-content="<?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_FMP_DESC');?>"><?php echo $this->googleData['lighthouseResult']['audits']['first-meaningful-paint']['displayValue'];?></span>
				</td>
				<td>
					<span data-content="<?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_FCI_DESC');?>"><?php echo $this->googleData['lighthouseResult']['audits']['first-cpu-idle']['displayValue'];?></span>
				</td>
				<td>
					<span data-content="<?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_TTI_DESC');?>"><?php echo $this->googleData['lighthouseResult']['audits']['interactive']['displayValue'];?></span>
				</td>
				<td>
					<span data-content="<?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_MPFID_DESC');?>"><?php echo $this->googleData['lighthouseResult']['audits']['interactive']['displayValue'];?></span>
				</td>
				<td>
					<span data-content="<?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_JSEXEC_DESC');?>"><?php echo $this->googleData['lighthouseResult']['audits']['bootup-time']['displayValue'];?></span>
				</td>
			<?php endif;?>
		</tr>
		<?php 
		$arrayPerformanceReportTests = array(
				array(	'testName' => 'render-blocking-resources',
					  	'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_RENDER_BLOCKING_RESOURCES',
					  	'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_RENDER_BLOCKING_RESOURCES_DESC'
				),
				array(	'testName' => 'uses-optimized-images',
						'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_USES_OPTIMIZED_IMAGES',
						'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_USES_OPTIMIZED_IMAGES_DESC'
				),
				array(	'testName' => 'uses-text-compression',
						'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_USES_TEXT_COMPRESSION',
						'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_USES_TEXT_COMPRESSION_DESC'
				),
				array(	'testName' => 'offscreen-images',
						'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_OFFSCREEN_IMAGES',
						'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_OFFSCREEN_IMAGES_DESC'
				),
				array(	'testName' => 'unminified-css',
						'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_UNMINIFIED_CSS',
						'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_UNMINIFIED_CSS_DESC'
				),
				array(	'testName' => 'unminified-javascript',
						'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_UNMINIFIED_JAVASCRIPT',
						'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_UNMINIFIED_JAVASCRIPT_DESC'
				),
				array(	'testName' => 'redirects',
						'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_REDIRECTS',
						'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_REDIRECTS_DESC'
				),
				array(	'testName' => 'time-to-first-byte',
						'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_REDIRECTS',
						'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_REDIRECTS_DESC'
				),
				array(	'testName' => 'max-potential-fid',
						'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_MAX_POTENTIAL_FID',
						'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_MAX_POTENTIAL_FID_DESC'
				),
				array(	'testName' => 'third-party-summary',
						'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_THIRD_PARTY_SUMMARY',
						'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_THIRD_PARTY_SUMMARY_DESC'
				),
				array(	'testName' => 'total-blocking-time',
						'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_TBT',
						'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_TBT_DESC'
				),
				array(	'testName' => 'efficient-animated-content',
						'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_EAC',
						'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_EAC_DESC'
				)
		);?>
	</tbody>
</table>

<table>
	<tbody>
		<tr>
			<th>
				<?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_AUDIT_TEST_TITLE' ); ?>
			</th>
			<th>
				<?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_AUDIT_TEST_STATUS' ); ?>
			</th>
		</tr>
		
		<?php
		if(isset($this->googleData['lighthouseResult']['audits'])):
			foreach ($arrayPerformanceReportTests as $testToExec):?>
			<tr>
				<td>
					<span data-content="<?php echo JText::_($testToExec['testDescription']);?>"><?php echo JText::_($testToExec['testTitle']); ?></span>
				</td>
				<td>
					<?php if($this->googleData['lighthouseResult']['audits'][$testToExec['testName']]['score'] > 0) {
						echo $successTest;
					} elseif($this->googleData['lighthouseResult']['audits'][$testToExec['testName']]['score'] == '0') {
						echo $failedTest;
					} elseif($this->googleData['lighthouseResult']['audits'][$testToExec['testName']]['score'] == '') {
						echo $notavailableTest;
					}
					?>
				</td>
			</tr>
			<?php
		endforeach;
	endif;
	?>
	</tbody>
</table>

<!-- PAGESPEED ASSETS REPORT BY API RESPONSE-->
<p></p>
<b><font size="3" color="#0028D3"><?php echo JText::_ ('COM_JMAP_GOOGLE_PAGESPEED_ASSETS_REPORT' ) . $reportDelimiter;?></font></b>
<table>
	<thead>
		<tr>
			<th>
				<span><?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_ASSETS_REPORT_URL' ); ?></span>
			</th>
			<th>
				<span><?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_ASSETS_REPORT_TYPE' ); ?></span>
			</th>
			<th>
				<span><?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_ASSETS_REPORT_MIMETYPE' ); ?></span>
			</th>
			<th>
				<span><?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_ASSETS_REPORT_STATUSCODE' ); ?></span>
			</th>
			<th>
				<span><?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_ASSETS_REPORT_SIZE' ); ?></span>
			</th>
		</tr>
	</thead>
	
	<tbody>
		<?php
		if(isset($this->googleData['lighthouseResult']['audits'])):
			foreach($this->googleData['lighthouseResult']['audits']['network-requests']['details']['items'] as $asset):?>
				<tr>
					<td>
						<?php echo $asset['url'];?>
					</td>
					<td>
						<span><?php echo isset($asset['resourceType']) ? JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_ASSET_' . JString::strtoupper(JString::str_ireplace('_', '', $asset['resourceType']))) : JText::_('COM_JMAP_NA'); ?></span>
					</td>
					<td>
						<span><?php echo isset($asset['mimeType']) ? $asset['mimeType'] : JText::_('COM_JMAP_NA'); ?></span>
					</td>
					<td>
						<?php
							$assetColor = '#428bca';
							$statusCode = (int)$asset['statusCode'];
							if($statusCode <= 0) {
								$statusCode = JText::_('COM_JMAP_NA');
								$assetColor = '#428bca';
							}elseif($statusCode > 0 && $statusCode < 300) {
								$assetColor = '#3c763d'; 
							} elseif ($statusCode >= 300 && $statusCode < 500) {
								$assetColor = '#f89406';
							} elseif ($statusCode >= 500) {
								$assetColor = '#d30000';
							}
						?>
						<font color="<?php echo $assetColor;?>"><?php echo $statusCode; ?></font>
					</td>
					
					<td>
						<span><?php echo number_format($asset['transferSize'] / 1024); ?></span>
					</td>
				</tr>
			<?php
			endforeach;
		endif;
		?>
	</tbody>
</table>

<!-- PAGESPEED SEO REPORT BY API RESPONSE-->
<p></p>
<b><font size="3" color="#0028D3"><?php echo JText::_ ('COM_JMAP_GOOGLE_PAGESPEED_SEO_REPORT' ) . $reportDelimiter;?></font></b>
<table>
		<?php 
		$arraySeoReportTests = array(
			array(	'testName' => 'document-title',
				  	'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_DOCUMENT_TITLE',
				  	'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_DOCUMENT_TITLE_DESC'
			),
			array(	'testName' => 'meta-description',
					'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_META_DESCRIPTION',
					'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_META_DESCRIPTION_DESC'
			),
			array(	'testName' => 'viewport',
					'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_VIEWPORT',
					'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_VIEWPORT_DESC'
			),
			array(	'testName' => 'canonical',
					'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_CANONICAL',
					'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_CANONICAL_DESC'
			),
			array(	'testName' => 'hreflang',
					'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_HREFLANG',
					'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_HREFLANG_DESC'
			),
			array(	'testName' => 'is-crawlable',
					'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_ISCRAWABLE',
					'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_ISCRAWABLE_DESC'
			),
			array(	'testName' => 'tap-targets',
					'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_TAPTARGETS',
					'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_TAPTARGETS_DESC'
			),
			array(	'testName' => 'font-size',
					'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_FONTSIZE',
					'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_FONTSIZE_DESC'
			),
			array(	'testName' => 'structured-data',
					'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_STRUCTURED_DATA',
					'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_STRUCTURED_DATA_DESC'
			),
			array(	'testName' => 'robots-txt',
					'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_ROBOTSTXT',
					'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_ROBOTSTXT_DESC'
			),
			array(	'testName' => 'link-text',
					'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_LINKTEXT',
					'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_LINKTEXT_DESC'
			),
			array(	'testName' => 'http-status-code',
					'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_HTTP_STATUS_CODE',
					'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_HTTP_STATUS_CODE_DESC'
			),
			array(	'testName' => 'image-alt',
					'testTitle' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_IMAGEALT',
					'testDescription' => 'COM_JMAP_GOOGLE_PAGESPEED_REPORT_IMAGEALT_DESC'
			)
	);?>
	
	<thead>
		<tr>
			<th>
				<?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_SEO_TEST_TITLE' ); ?>
			</th>
			<th>
				<?php echo JText::_('COM_JMAP_GOOGLE_PAGESPEED_REPORT_SEO_TEST_STATUS' ); ?>
			</th>
		</tr>
	</thead>
	
	<tbody>
		<?php
		if(isset($this->googleData['lighthouseResult']['audits'])):
			foreach ($arraySeoReportTests as $testToExec):?>
				<tr>
					<td>
						<span data-content="<?php echo JText::_($testToExec['testDescription']);?>"><?php echo JText::_($testToExec['testTitle']); ?></span>
					</td>
					<td>
						<?php if($this->googleData['lighthouseResult']['audits'][$testToExec['testName']]['score'] > 0) {
							echo $successTest;
						} elseif($this->googleData['lighthouseResult']['audits'][$testToExec['testName']]['score'] == '0') {
							echo $failedTest;
						} elseif($this->googleData['lighthouseResult']['audits'][$testToExec['testName']]['score'] == '') {
							echo $notavailableTest;
						}
						?>
					</td>
				</tr>
			<?php
			endforeach;
		endif;
		?>
	</tbody>
</table>

</body>

</html>