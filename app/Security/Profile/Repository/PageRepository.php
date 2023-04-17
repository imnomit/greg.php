<?php

namespace Application\Security\Profile\Repository;

use Application\Model\Page\Discussion\DiscussionModel;
use Application\Model\Page\PageModel;
use Application\Model\Page\Participant\ParticipantModel;
use Application\Model\Page\Revision\RevisionModel;
use nomit\Database\Table\ActiveRow;
use nomit\Security\Authentication\Token\Storage\TokenStorageInterface;
use nomit\Security\Authentication\User\UserProviderInterface;
use nomit\Security\Profile\Repository\Repository;

final class PageRepository extends Repository
{

    public function __construct(
        private UserProviderInterface $userProvider,
        private PageModel $pageModel,
        private DiscussionModel $discussionModel,
        private ParticipantModel $participantModel,
        private RevisionModel $revisionModel,
    )
    {
        parent::__construct();
    }

    public function initialize(int $userId): \nomit\Security\Profile\Repository\RepositoryInterface
    {
        $this->initializePages($userId);
        $this->initializeDiscussions($userId);

        return $this;
    }

    private function initializePages(int $userId): void
    {
        $revisions = $this->getRevisions($userId);
        $participants = $this->getParticipants($userId);
        $pages = [];

        foreach($revisions as $revision) {
            if(isset($pages[$revision->page_id])) {
                continue;
            }

            $pages[$revision->page_id] = $this->pageModel->getById($revision->page_id);
        }

        foreach($participants as $participant) {
            if(isset($pages[$participant->page_id])) {
                continue;
            }

            $pages[$participant->page_id] = $this->pageModel->getById($participant->page_id);
        }

        $this->set('pages', array_map(function($page) {
            return $this->extendPage($page);
        }, $pages));
    }

    private function getRevisions(int $userId): array
    {
        return $this->revisionModel->getByUserId($userId);
    }

    private function getParticipants(int $userId): array
    {
        return $this->participantModel->getByUserId($userId);
    }

    private function initializeDiscussions(int $userId): void
    {
        $discussionRows = $this->discussionModel->getByUserId($userId);
        $discussions = [];

        foreach($discussionRows as $discussionRow) {
            $discussions[$discussionRow->id] = $discussionRow->toArray();
        }

        $this->set('discussions', $discussions);
    }

    private function extendPage(ActiveRow $page): array
    {
        $page = $page->toArray();

        $page['revision'] = $this->extendRevision($this->revisionModel->getByPageId($page['id']));
        $page['revisions'] = array_map(function ($revision) {
            return $this->extendRevision($revision);
        }, $this->revisionModel->getAllByPageId($page['id']));

        return $page;
    }

    private function extendRevision(ActiveRow|null $revisionRow): ?array
    {
        if(!$revisionRow) {
            return null;
        }

        $revision = $revisionRow->toArray();

        $revision['user'] = $this->userProvider->getByUserId($revisionRow->user_id)?->toArray();

        return $revision;
    }

}