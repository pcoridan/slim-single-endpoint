<?php

class ScoreMapper extends Mapper
{


    public function save(ScoreEntity $score) {
        $sql = "INSERT INTO score
            (first_name, last_name, score) values
            (:first_name, :last_name, :score)";

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            "first_name" => $score->getFirstName(),
            "last_name" => $score->getLastName(),
            "score" => $score->getScore(),
        ]);

        if(!$result) {
            throw new Exception("could not save record");
        }
    }
}
