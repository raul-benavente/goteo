<?php

namespace Goteo\Repository;

use Goteo\Application\Exception\ModelException;
use Goteo\Core\Exception;
use Goteo\Entity\ImpactData\ImpactDataProject;
use Goteo\Model\ImpactData;
use Goteo\Model\Project;
use Goteo\Model\Project\Cost;

class ImpactDataProjectRepository extends BaseRepository
{
    protected ?string $table = 'impact_data_project';

    /**
     * @return ImpactDataProject[]
     */
    public function getListByProject(Project $project): array
    {
        $sql = "SELECT *
                FROM $this->table
                WHERE project_id = ?
        ";

        $list = [];
        try {
            foreach($this->query($sql, [$project->id])->fetchAll(\PDO::FETCH_OBJ) as $obj) {
                $impactDataProject = new ImpactDataProject();
                $impactData = ImpactData::get($obj->impact_data_id);
                $cost = Cost::get($obj->cost);

                $impactDataProject->setImpactData($impactData)->setProject($project)->setCost($cost)->setValue($obj->value);
                $list[] = $impactDataProject;
            }
        } catch (\PDOException $e) {
            return [];
        }

        return $list;
    }

    public function count(Project $project): int
    {
        $sql = "SELECT count(*)
                FROM $this->table
                WHERE project_id = ?
        ";

        return $this->query($sql, [$project->id])->fetchColumn();
    }

    public function exists(ImpactData $impactData, Project $project): bool
    {
        $sql = "SELECT *
                FROM $this->table
                WHERE impact_data_id = :impact_data_id AND project_id = :project_id
        ";

        $values = [
                ':impact_data_id' => $impactData->id,
                ':project_id' => $project->id
        ];

        return (bool) $this->query($sql, $values);
    }

    public function persist(ImpactDataProject $impactDataProject, array &$errors = []): ?ImpactDataProject
    {
        $fields = [
            'impact_data_id' => ':impact_data_id',
            'project_id' => ':project_id',
            'value' => ':value',
            'cost_id' => ':cost_id'
        ];

        $values = [
            ':impact_data_id' => $impactDataProject->getImpactData()->id,
            ':project_id' => $impactDataProject->getProject()->id,
            ':value' => $impactDataProject->getValue(),
            ':cost_id' => $impactDataProject->getCost()->id
        ];

        $sql = "REPLACE INTO `$this->table` (" . implode(',', array_keys($fields)) . ") VALUES (" . implode(',', array_values($fields)) . ")";

        try {
            $this->query($sql, $values);
        } catch (\PDOException $e) {
            $errors[] = $e->getMessage();
            return null;
        }

        return $impactDataProject;
    }

    /**
     * @throws ModelException
     */
    public function delete(ImpactDataProject $impactDataProject): void
    {
        $sql = "DELETE FROM `$this->table` WHERE `impact_data_id` = :impact_data_id AND `project_id` = :project_id";
        $values = [
                ':impact_data_id' => $impactDataProject->getImpactData()->id,
                ':project_id' => $impactDataProject->getProject()->id
        ];

        try {
            $this->query($sql, $values);
        } catch (\PDOException $e) {
            throw new ModelException($e->getMessage());
        }
    }
}