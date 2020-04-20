<?php

declare(strict_types=1);

use Phalcon\Di;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Query;
use Phalcon\Mvc\Model\Manager;
use Phalcon\Http\Request;
use Phalcon\Mvc\View;

/**
 * @property Di   $di
 * @property View $view
 */

class PhqlController extends ControllerBase
{

    public function indexAction()
    {
    }

    public function queryAction()
    {
        // PHQL Query
        $query = new Query('SELECT * FROM Hostels', $this->di);

        $result = $query->execute();
        $this->view->setVar('result', $result);
    }

    public function modelmanagerAction()
    {
        // PHQL Models Manager
        $query = $this->modelsManager->createQuery('SELECT * FROM Hostels');

        $result = $query->execute();
        $this->view->setVar('result', $result);
    }

    public function boundparamAction()
    {
        // PHQL Models Manager (Bound Parameter)
        $hostel_id = $this->request->getQuery('hostel_id');

        $query = $this->modelsManager->createQuery('SELECT * FROM Hostels WHERE ID = :id:');

        // Bound parameter
        $result = $query->execute(['id' => $hostel_id]);
        $this->view->setVar('result', $result);
    }

    public function aliasesAction()
    {
        // PHQL Aliases
        $query = $this->modelsManager->createQuery('SELECT h.ID, h.hostel_name, h.city, h.price_from FROM Hostels h');

        $result = $query->execute();
        $this->view->setVar('result', $result);
    }

    public function aggregateAction()
    {

        // PHQL Aggregations
        $city_name = $this->request->getQuery('city');
        $query = $this->modelsManager->createQuery(
            "SELECT AVG(price_from) as price_average, 
        COUNT(hostel_name) as hostel_count,
        MIN(price_from) as price_min,
        MAX(price_from) as price_max
         FROM Hostels
         WHERE city = :city:"
        );

        $data_query = $this->modelsManager->createQuery("SELECT * FROM Hostels WHERE city = :city:");


        $result = $query->execute(['city' => $city_name]);
        $datas = $data_query->execute(['city' => $city_name]);

        $this->view->setVar('result', $result);
        $this->view->setVar('city', $city_name);
        $this->view->setVar('datas', $datas);
    }

    public function insertAction()
    {
        $request = $this->request->getPost();

        $phql =
            "INSERT INTO Hostels 
        (hostel_name, city, price_from) 
        VALUES 
        (?0, ?1, ?2)";

        $records = $this
            ->modelsManager
            ->executeQuery(
                $phql,
                [
                    0 => $request['hostel_name'],
                    1 => $request['city'],
                    2 => $request['price_from']
                ]
            );

        $this->view->setVar('result', $records);
    }

    public function updateAction()
    {
        $request = $this->request->getPost();
        $target_column = $request['target_column'];
        $phql =
            "UPDATE Hostels
        SET " . $target_column . " = :new_value:
        WHERE ID = :id:";

        $query = $this->modelsManager->createQuery(
            $phql
        );

        $data_query = $this->modelsManager->createQuery("SELECT * FROM Hostels WHERE ID = :id:");

        $status = $query->execute(
            [
                'new_value' => $request['new_value'],
                'id' => $request['ID']
            ]
        );

        $result = $data_query->execute(
            [
                'id' => $request['ID']
            ]
        );

        $this->view->setVar('result', $result);
        $this->view->setVar('status', $status);
    }

    public function deleteAction()
    {
        $request = $this->request->getQuery('ID');

        $data_query = $this->modelsManager->createQuery("SELECT * FROM Hostels WHERE ID = :id:");
        $result = $data_query->execute(['id' => $request]);

        $query = $this->modelsManager->createQuery("DELETE FROM Hostels WHERE ID = :id:");
        $status = $query->execute(['id' => $request]);

        $this->view->setVar('result', $result);
        $this->view->setVar('status', $status);
    }

    public function querybuilderAction()
    {
        // No Parameter
        // $result = $this
        //     ->modelsManager
        //     ->createBuilder()
        //     ->from(Hostels::class)
        //     ->orderBy('city')
        //     ->getQuery()
        //     ->execute();

        // Bound Parameter
        $result = $this
            ->modelsManager
            ->createBuilder()
            ->from(Hostels::class)
            ->where('ID = :id:')
            ->getQuery()
            ->execute(
                [
                    'id' => 1
                ]
            );

        $this->view->setVar('result', $result);
    }

    public function disableliteralAction()
    {
        //// Test Injection ' OR '' = '
        //// Activate disableliterals
        Model::setup(
            [
                'phqlLiterals' => false
            ]
        );



        //// Without disableliteral
        // $request = $this->request->getQuery('ID');
        // $phql = "SELECT * FROM Hostels WHERE ID = '$request'";

        // $result = $this->modelsManager->executeQuery($phql);

        //// With disableliteral
        $request = $this->request->getQuery('ID');
        $phql = "SELECT * FROM Hostels WHERE ID = :id:";

        $result = $this->modelsManager->executeQuery($phql, ['id' => $request]);

        $this->view->setVar('result', $result);
    }

    public function reservedwordsAction()
    {
        $phql = "SELECT ID, hostel_name, [like] FROM Hostels";

        $result = $this->modelsManager->executeQuery($phql);

        $this->view->setVar('result', $result);
    }

    public function rawsqlAction()
    {
        $request = $this->request->getQuery('ID');
        $result = Hostels::findHostelByID($request);

        $this->view->setVar('result', $result);
    }
}
