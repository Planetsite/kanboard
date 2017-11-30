<?php

namespace Kanboard\Controller;

use Kanboard\Filter\TaskProjectFilter;
use Kanboard\Model\TaskModel;

/**
 * Planet Task List Controller
 *
 * @package  Kanboard\Controller
 * @author   Frederic Guillot
 */
class PlanetTaskListController extends BaseController
{
    /**
     * Show list view for projects
     *
     * @access public
     */
    public function show()
    {
        $project = $this->getProject();
        $search = $this->helper->projectHeader->getSearchQuery($project);
        
        $paginator = $this->paginator
            ->setUrl('PlanetTaskListController', 'show', array('project_id' => $project['id']))
            ->setMax(200)
            ->setOrder(TaskModel::TABLE.'.id')
            ->setDirection('DESC')
            ->setQuery($this->taskLexer
                ->build($search)
                //->withFilter(new TaskProjectFilter($project['id']))
                //->withFilter(new TaskProjectFilter($project['id']))
                ->getQuery()
            )
            ->calculate();

        $this->response->html($this->helper->layout->app('task_list/show_all', array(
            'project' => $project,
            'title' => $project['name'],
            'description' => $this->helper->projectHeader->getDescription($project),
            'paginator' => $paginator,
        )));
    }
}
