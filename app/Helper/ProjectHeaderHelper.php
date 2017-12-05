<?php

namespace Kanboard\Helper;

use Kanboard\Core\Base;

/**
 * Project Header Helper
 *
 * @package helper
 * @author  Frederic Guillot
 */
class ProjectHeaderHelper extends Base
{
    /**
     * Get current search query
     *
     * @access public
     * @param  array  $project
     * @return string
     */
    public function getSearchQuery(array $project)
    {
        $search = $this->request->getStringParam('search', $this->userSession->getFilters($project['id']));
        $this->userSession->setFilters($project['id'], $search);
        return urldecode($search);
    }

    /**
     * Render project header (views switcher and search box)
     *
     * @access public
     * @param  array  $project
     * @param  string $controller
     * @param  string $action
     * @param  bool   $boardView
     * @return string
     */
    public function render(array $project, $controller, $action, $boardView = false)
    {
        $filters = array(
            'controller' => $controller,
            'action' => $action,
            'project_id' => $project['id'],
            'search' => $this->getSearchQuery($project),
        );
    
        $firstProject = null;
        
        $projects_list = [];
        foreach($this->projectModel->getAll() as $projectItem){
            $firstProject = $projectItem;
            $projects_list[$projectItem['id']] = $projectItem['name'];
        }
    
        $columns_list = [];
        foreach($this->columnModel->getAll($firstProject['id']) as $columnItem){
            $columns_list[$columnItem['id']] = $columnItem['title'];
        }
        
        return $this->template->render('project_header/header', array(
            'project' => $project,
            'filters' => $filters,
            'categories_list' => $this->categoryModel->getList($project['id'], false),
            'users_list' => $this->projectUserRoleModel->getAssignableUsersList($project['id'], false),
            'columns_list' => $columns_list,
            'projects_list' => $projects_list,
            'custom_filters_list' => $this->customFilterModel->getAll($project['id'], $this->userSession->getId()),
            'board_view' => $boardView,
        ));
    }

    /**
     * Get project description
     *
     * @access public
     * @param  array  &$project
     * @return string
     */
    public function getDescription(array &$project)
    {
        if ($project['owner_id'] > 0) {
            $description = t('Project owner: ').'**'.$this->helper->text->e($project['owner_name'] ?: $project['owner_username']).'**'.PHP_EOL.PHP_EOL;

            if (! empty($project['description'])) {
                $description .= '***'.PHP_EOL.PHP_EOL;
                $description .= $project['description'];
            }
        } else {
            $description = $project['description'];
        }

        return $description;
    }
}
