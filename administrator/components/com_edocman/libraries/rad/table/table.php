<?php
/**
 * @package     Joomla.OS
 * @subpackage  Table
 * @author      Ossolution Team
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die();

/**
 * Since the JTable class is marked as abstract, we need to define this package so that we can create a JTable object without having to creating a file for it.
 * Simply using the syntax $row = new OSTable('#__mycom_mytable', 'id', $db);
 *
 */
class OSTable extends JTable
{
}
