<?php
/**
 * @package         Snippets
 * @version         9.3.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\Text as JText;
use RegularLabs\Component\Snippets\Administrator\Model\ItemModel;
use RegularLabs\Library\Alias as RL_Alias;
use RegularLabs\Library\Date as RL_Date;
use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\Parameters as RL_Parameters;
use RegularLabs\Library\RegEx as RL_RegEx;
use RegularLabs\Library\User as RL_User;

$params  = RL_Parameters::getComponent('snippets');
$contact = (object) [];

$db         = RL_DB::get();
$table_name = $db->getPrefix() . 'contact_details';

if (in_array($table_name, $db->getTableList()))
{
    $query = 'SHOW FIELDS FROM ' . $db->quoteName($table_name);
    $db->setQuery($query);
    $columns = $db->loadColumn();

    if (in_array('misc', $columns))
    {
        $query = $db->getQuery(true)
            ->select('c.misc')
            ->from('#__contact_details as c')
            ->where('c.user_id = ' . RL_User::getId());
        $db->setQuery($query);
        $contact = $db->loadObject();
    }
}

$editor_name = RL_Input::getString('editor', 'text');
// Remove any dangerous character to prevent cross site scripting
$editor_name = RL_RegEx::replace('[\'\";\s]', '', $editor_name);

$yes = '<td class="text-center text-success"><span class="icon-checkmark"></span> ' . JText::_('JYES') . '</td>';
$no  = '<td class="text-center text-danger" class="text-muted"><span class="icon-cancel"></span> ' . JText::_('JNO') . '</td>';

[$char_start, $char_end] = explode('.', $this->config->tag_characters_dynamic);

$item = (new ItemModel)->getItem(RL_Input::getInt('id'));
?>
    <div class="alert alert-danger">
        <?php echo JText::_('RL_ONLY_AVAILABLE_IN_PRO'); ?>
    </div>

<?php if ( ! empty($item->variables)) : ?>
    <h2><?php echo JText::_('SNP_VARIABLES'); ?></h2>

    <table class="table table-striped">
        <thead>
            <tr>
                <th class="w-1 text-center">&nbsp;</th>
                <th class="fw-bold">
                    <?php echo JText::_('SNP_KEY'); ?>
                </th>
                <th class="fw-bold">
                    <span><?php echo JText::_('SNP_DEFAULT_VALUE'); ?></span>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($item->variables as $variable) : ?>
                <tr>
                    <td><?php echo renderInsertButtonVariable($editor_name, $variable->key); ?></td>
                    <td><code class="text-nowrap"><code><?php echo $variable->key; ?></code></td>
                    <td><?php echo $variable->default; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2 class="mt-4"><?php echo JText::_('RL_DYNAMIC_TAGS'); ?></h2>

<?php endif; ?>

    <p><?php echo JText::_('SNP_DYNAMIC_TAGS_DESC'); ?></p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th class="w-1 text-center">&nbsp;</th>
                <th class="fw-bold">
                    <?php echo JText::_('RL_INPUT_SYNTAX'); ?>
                </th>
                <th class="fw-bold">
                    <span><?php echo JText::_('RL_OUTPUT_EXAMPLE'); ?></span>
                </th>
                <th class="fw-bold">
                    <span><?php echo JText::_('JGLOBAL_DESCRIPTION'); ?></span>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo renderInsertButtonDynamic($editor_name, $params->tag . ':...'); ?></td>
                <td><code class="text-nowrap"><?php echo $char_start; ?><?php echo $params->tag; ?>
                        :another-snippet<?php echo $char_end; ?></code></td>
                <td>I'm another snippet</td>
                <td>
                    <?php echo JText::_('SNP_DYNAMIC_TAG_SNIPPET'); ?>
                </td>
            </tr>
            <tr>
                <td><?php echo renderInsertButtonDynamic($editor_name, 'user:id'); ?></td>
                <td><code class="text-nowrap"><?php echo $char_start; ?>user:id<?php echo $char_end; ?></code></td>
                <td><?php echo RL_User::getId(); ?></td>
                <td>
                    <?php echo JText::_('RL_DYNAMIC_TAG_USER_ID'); ?>
                    <br><em class="text-muted"><?php echo JText::_('RL_DYNAMIC_TAG_USER_TAG_DESC'); ?></em>
                </td>
            </tr>
            <tr>
                <td><?php echo renderInsertButtonDynamic($editor_name, 'user:username'); ?></td>
                <td><code class="text-nowrap"><?php echo $char_start; ?>user:username<?php echo $char_end; ?></code>
                </td>
                <td><?php echo RL_User::getUsername(); ?></td>
                <td>
                    <?php echo JText::_('RL_DYNAMIC_TAG_USER_USERNAME'); ?>
                    <br><em class="text-muted"><?php echo JText::_('RL_DYNAMIC_TAG_USER_TAG_DESC'); ?></em>
                </td>
            </tr>
            <tr>
                <td><?php echo renderInsertButtonDynamic($editor_name, 'user:name'); ?></td>
                <td><code class="text-nowrap"><?php echo $char_start; ?>user:name<?php echo $char_end; ?></code></td>
                <td><?php echo RL_User::getName(); ?></td>
                <td>
                    <?php echo JText::_('RL_DYNAMIC_TAG_USER_NAME'); ?>
                    <br><em class="text-muted"><?php echo JText::_('RL_DYNAMIC_TAG_USER_TAG_DESC'); ?></em>
                </td>
            </tr>
            <tr>
                <td><?php echo renderInsertButtonDynamic($editor_name, 'user:misc'); ?></td>
                <td><code class="text-nowrap"><?php echo $char_start; ?>user:misc<?php echo $char_end; ?></code></td>
                <td><?php echo $contact->misc ?? ''; ?></td>
                <td>
                    <?php echo JText::_('RL_DYNAMIC_TAG_USER_OTHER'); ?>
                    <br><em class="text-muted"><?php echo JText::_('RL_DYNAMIC_TAG_USER_TAG_DESC'); ?></em>
                </td>
            </tr>
            <tr>
                <td><?php echo renderInsertButtonDynamic($editor_name, 'article:id'); ?></td>
                <td><code class="text-nowrap"><?php echo $char_start; ?>article:id<?php echo $char_end; ?></code></td>
                <td>123</td>
                <td><?php echo JText::_('RL_DYNAMIC_TAG_ARTICLE_ID'); ?></td>
            </tr>
            <tr>
                <td><?php echo renderInsertButtonDynamic($editor_name, 'article:title'); ?></td>
                <td><code class="text-nowrap"><?php echo $char_start; ?>article:title<?php echo $char_end; ?></code>
                </td>
                <td>My Article</td>
                <td><?php echo JText::_('RL_DYNAMIC_TAG_ARTICLE_TITLE'); ?></td>
            </tr>
            <tr>
                <td><?php echo renderInsertButtonDynamic($editor_name, 'article:alias'); ?></td>
                <td><code class="text-nowrap"><?php echo $char_start; ?>article:alias<?php echo $char_end; ?></code>
                </td>
                <td>my-article</td>
                <td><?php echo JText::_('RL_DYNAMIC_TAG_ARTICLE_OTHER'); ?></td>
            </tr>
            <tr>
                <td><?php echo renderInsertButtonDynamic($editor_name, 'date:%A, %d %B %Y'); ?></td>
                <td><code class="text-nowrap"><?php echo $char_start; ?>date:%A, %d %B %Y<?php echo $char_end; ?></code>
                </td>
                <td>
                    <?php echo date(RL_Date::strftimeToDateFormat('%A, %d %B %Y')); ?>
                </td>
                <td><?php echo JText::sprintf('RL_DYNAMIC_TAG_DATE', '<a href="http://www.php.net/manual/function.strftime.php" target="_blank">', '</a>', '<code><?php echo $char_start; ?>date:%A, %d %B %Y<?php echo $char_end; ?></code>'); ?></td>
            </tr>
            <tr>
                <td><?php echo renderInsertButtonDynamic($editor_name, 'date:%Y-%m-%d'); ?></td>
                <td><code class="text-nowrap"><?php echo $char_start; ?>date:%Y-%m-%d<?php echo $char_end; ?></code>
                </td>
                <td>
                    <?php echo date(RL_Date::strftimeToDateFormat('%Y-%m-%d')); ?>
                </td>
                <td><?php echo JText::sprintf('RL_DYNAMIC_TAG_DATE', '<a href="http://www.php.net/manual/function.strftime.php" target="_blank">', '</a>', '<code><?php echo $char_start; ?>date:%Y-%m-%d<?php echo $char_end; ?></code>'); ?></td>
            </tr>
            <tr>
                <td><?php echo renderInsertButtonDynamic($editor_name, 'random:0-100'); ?></td>
                <td><code class="text-nowrap">
                        <?php echo $char_start; ?>random:0-100<?php echo $char_end; ?><br>
                        <?php echo $char_start; ?>random:1000-9999<?php echo $char_end; ?>
                    </code></td>
                <td>
                    <?php echo rand(0, 100); ?><br>
                    <?php echo rand(1000, 9999); ?>
                </td>
                <td><?php echo JText::_('RL_DYNAMIC_TAG_RANDOM'); ?></td>
            </tr>
            <tr>
                <td><?php echo renderInsertButtonDynamic($editor_name, 'random:this,that'); ?></td>
                <td><code class="text-nowrap">
                        <?php echo $char_start; ?>random:this,that<?php echo $char_end; ?><br>
                        <?php echo $char_start; ?>random:1-10,20,50,100<?php echo $char_end; ?>
                    </code></td>
                <td>
                    <?php
                    $values = ['this', 'that'];
                    echo $values[rand(0, count($values) - 1)];
                    ?>
                    <br>

                    <?php
                    $values = [rand(1, 10), 20, 50, 100];
                    echo $values[rand(0, count($values) - 1)];
                    ?>
                </td>
                <td><?php echo JText::_('RL_DYNAMIC_TAG_RANDOM_LIST'); ?></td>
            </tr>
            <tr>
                <td><?php echo renderInsertButtonDynamic($editor_name, 'counter'); ?></td>
                <td><code class="text-nowrap"><?php echo $char_start; ?>counter<?php echo $char_end; ?></code></td>
                <td>1</td>
                <td><?php echo JText::_('RL_DYNAMIC_TAG_COUNTER'); ?></td>
            </tr>
            <tr>
                <td><?php echo renderInsertButtonDynamicWrap($editor_name, 'escape'); ?></td>
                <td>
                    <code class="text-nowrap"><?php echo $char_start; ?>
                        escape<?php echo $char_end; ?>&hellip;<?php echo $char_start; ?>
                        /escape<?php echo $char_end; ?></code>
                </td>
                <td><?php echo addslashes(html_entity_decode(JText::_('RL_DYNAMIC_TAG_STRING_EXAMPLE'))); ?></td>
                <td><?php echo JText::_('RL_DYNAMIC_TAG_ESCAPE'); ?></td>
            </tr>
            <tr>
                <td><?php echo renderInsertButtonDynamicWrap($editor_name, 'uppercase'); ?></td>
                <td>
                    <code class="text-nowrap"><?php echo $char_start; ?>
                        uppercase<?php echo $char_end; ?>&hellip;<?php echo $char_start; ?>
                        /uppercase<?php echo $char_end; ?></code>
                </td>
                <td><?php echo strtoupper(JText::_('RL_DYNAMIC_TAG_STRING_EXAMPLE')); ?></td>
                <td><?php echo JText::_('RL_DYNAMIC_TAG_UPPERCASE'); ?></td>
            </tr>
            <tr>
                <td><?php echo renderInsertButtonDynamicWrap($editor_name, 'lowercase'); ?></td>
                <td>
                    <code class="text-nowrap"><?php echo $char_start; ?>
                        lowercase<?php echo $char_end; ?>&hellip;<?php echo $char_start; ?>
                        /lowercase<?php echo $char_end; ?></code>
                </td>
                <td><?php echo strtolower(JText::_('RL_DYNAMIC_TAG_STRING_EXAMPLE')); ?></td>
                <td><?php echo JText::_('RL_DYNAMIC_TAG_LOWERCASE'); ?></td>
            </tr>
            <tr>
                <td><?php echo renderInsertButtonDynamicWrap($editor_name, 'notags'); ?></td>
                <td>
                    <code class="text-nowrap"><?php echo $char_start; ?>
                        notags<?php echo $char_end; ?>&hellip;<?php echo $char_start; ?>
                        /notags<?php echo $char_end; ?></code>
                </td>
                <td><?php echo strip_tags(JText::_('RL_DYNAMIC_TAG_STRING_EXAMPLE')); ?></td>
                <td><?php echo JText::_('RL_DYNAMIC_TAG_NOTAGS'); ?></td>
            </tr>
            <tr>
                <td><?php echo renderInsertButtonDynamicWrap($editor_name, 'nowhitespace'); ?></td>
                <td>
                    <code class="text-nowrap"><?php echo $char_start; ?>
                        nowhitespace<?php echo $char_end; ?>&hellip;<?php echo $char_start; ?>
                        /nowhitespace<?php echo $char_end; ?></code>
                </td>
                <td><?php echo str_replace(' ', '', strip_tags(JText::_('RL_DYNAMIC_TAG_STRING_EXAMPLE'))); ?></td>
                <td><?php echo JText::_('RL_DYNAMIC_TAG_NOWHITESPACE'); ?></td>
            </tr>
            <tr>
                <td><?php echo renderInsertButtonDynamicWrap($editor_name, 'toalias'); ?></td>
                <td>
                    <code class="text-nowrap"><?php echo $char_start; ?>
                        toalias<?php echo $char_end; ?>&hellip;<?php echo $char_start; ?>
                        /toalias<?php echo $char_end; ?></code>
                </td>
                <td><?php echo RL_Alias::get(JText::_('RL_DYNAMIC_TAG_STRING_EXAMPLE')); ?></td>
                <td><?php echo JText::_('RL_DYNAMIC_TAG_TOALIAS'); ?></td>
            </tr>
        </tbody>
    </table>
<?php
function renderInsertButtonVariable($editor_name, $tag)
{
    return renderInsertButton(
        'parent.RegularLabs.SnippetsButton.insertTagVariable(\'' . $editor_name . '\', \'' . $tag . '\');'
    );
}

function renderInsertButtonDynamic($editor_name, $tag)
{
    return renderInsertButton(
        'parent.RegularLabs.SnippetsButton.insertTagDynamic(\'' . $editor_name . '\', \'' . $tag . '\');'
    );
}

function renderInsertButtonDynamicWrap($editor_name, $tag)
{
    return renderInsertButton(
        'parent.RegularLabs.SnippetsButton.insertTagDynamic(\'' . $editor_name . '\', \'' . $tag . '\', \'' . $tag . '\');',
        'RL_WRAP'
    );
}

function renderInsertButton($action, $text = 'RL_INSERT')
{
    return '<button onclick="' . $action . '"'
        . 'type="button" class="btn btn-secondary btn-sm text-nowrap">'
        . '<span class="fa fa-file-code me-1" aria-hidden="true"></span>'
        . JText::_($text)
        . '</button>';
}
