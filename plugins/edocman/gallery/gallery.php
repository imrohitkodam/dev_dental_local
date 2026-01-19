<?php
/**
 * @package            Joomla
 * @subpackage         Edocman
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2018 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class plgEdocmanGallery extends JPlugin
{
	/**
	 * Render setting form
	 *
	 * @param Edocman Document $row
	 *
	 * @return array
	 */
	public function onEditDocument($row)
	{
		if (JFactory::getApplication()->isSite())
		{
			return;
		}

		ob_start();
		$this->drawSettingForm($row);

		return array('title' => JText::_('EDOCMAN_GALLERY'),
		             'form'  => ob_get_clean(),
		);
	}

	/**
	 * Store selected images for event in galleries database
	 *
	 * @param EventbookingTableEvent $row
	 * @param bool                   $isNew true if create new event, false if edit
	 */
	public function onAfterSaveDocument($row, $data, $isNew)
	{
		if (JFactory::getApplication()->isSite())
		{
			return;
		}

		$images      = isset($data['gallery']) ? $data['gallery'] : JFactory::getApplication()->input->get('gallery', array(), 'array');
		$images      = array_filter($images);
		$ids         = [];
		$ordering    = 1;
		$thumbWidth  = $this->params->get('thumb_width', 150);
		$thumbHeight = $this->params->get('thumb_height', 150);


        foreach ($images as $image) {
            /* @var EventbookingTableGallery $rowGallery */
            $rowGallery = JTable::getInstance('Gallery', 'EDocmanTable');
            $rowGallery->bind($image);
            $rowGallery->document_id = $row->id;
            $rowGallery->ordering = $ordering++;
            $rowGallery->store();

            // Resize the image
            if ($rowGallery->image && file_exists(JPATH_ROOT . '/' . $rowGallery->image)) {
                $fileName = basename($rowGallery->image);
                $imagePath = JPATH_ROOT . '/' . $rowGallery->image;
                $thumbDir = JPATH_ROOT . '/' . substr($rowGallery->image, 0, strlen($rowGallery->image) - strlen($fileName)) . '/thumbs';

                if (!JFolder::exists($thumbDir)) {
                    JFolder::create($thumbDir);
                }

                $thumbImagePath = $thumbDir . '/' . $fileName;
                $fileExt = JFile::getExt($fileName);
                $image = new JImage($imagePath);

                if ($fileExt == 'PNG') {
                    $imageType = IMAGETYPE_PNG;
                } elseif ($fileExt == 'GIF') {
                    $imageType = IMAGETYPE_GIF;
                } elseif (in_array($fileExt, ['JPG', 'JPEG'])) {
                    $imageType = IMAGETYPE_JPEG;
                } else {
                    $imageType = '';
                }

                $image->cropResize($thumbWidth, $thumbHeight, false)
                    ->toFile($thumbImagePath, $imageType);
            }

            $ids[] = $rowGallery->id;
        }


		if (!$isNew)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->delete('#__edocman_galleries')
				->where('document_id = ' . $row->id);

			if (count($ids))
			{
				$query->where('id NOT IN (' . implode(',', $ids) . ')');
			}

			$db->setQuery($query)
				->execute();
		}
	}

	/**
	 * Display form allows users to change settings on subscription plan add/edit screen
	 *
	 * @param EventbookingTableEvent $row
	 */
	private function drawSettingForm($row)
	{
		$form                = JForm::getInstance('gallery', JPATH_ROOT . '/plugins/edocman/gallery/form/gallery.xml');
		$formData['gallery'] = [];

		// Load existing speakers for this document
		if ($row->id)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('*')
				->from('#__edocman_galleries')
				->where('document_id = ' . $row->id)
				->order('ordering');
			$db->setQuery($query);

			foreach ($db->loadObjectList() as $image)
			{
				$formData['gallery'][] = [
					'id'    => $image->id,
					'title' => $image->title,
					'image' => $image->image,
				];
			}
		}


		$form->bind($formData);

		foreach ($form->getFieldset() as $field)
		{
			echo $field->input;
		}
	}

	/**
	 * Display event gallery
	 *
	 * @param EventbookingTableEvent $row
	 *
	 * @return array|void
	 */
	public function onDocumentDisplay($row)
	{
		$eventId = $row->parent_id ?: $row->id;
		$db      = JFactory::getDbo();
		$query   = $db->getQuery(true)
			->select('*')
			->from('#__edocman_galleries')
			->where('document_id = ' . $eventId)
			->order('ordering');

		$db->setQuery($query);
		$images = $db->loadObjectList();


		if (empty($images))
		{
			return;
		}

		ob_start();
		$this->drawGallery($images);
		$form = ob_get_clean();

		return array('title'    => JText::_('EDOCMAN_GALLERY'),
		             'form'     => $form,
		);
	}

	/**
	 * Display event gallery
	 *
	 * @param array $images
	 *
	 * @throws Exception
	 */
	private function drawGallery($images)
	{
		$document = JFactory::getDocument();
		$rootUrl  = JUri::root(true);

		$document->addScript($rootUrl . '/components/com_edocman/assets/js/baguetteBox/baguetteBox.min.js');
		$document->addStyleSheet($rootUrl . '/components/com_edocman/assets/js/baguetteBox/baguetteBox.min.css');

		echo EDocmanHelperHtml::loadCommonLayout('common/gallery.php', ['images' => $images]);
	}
}
