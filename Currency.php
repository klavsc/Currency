<?php


use Sabre\Xml\Service;

Class Currency
{

    public function loadAll()
    {
        $input = file_get_contents('https://www.bank.lv/vk/ecb.xml');

        $service = new Service();
        $service->elementMap = [
            '{https://www.bank.lv/vk/ecb.xml}Currency' => 'Sabre\Xml\Element\KeyValue',
        ];

        $result = $service->parse($input);

        foreach ($result[1]['value'] as $item) {
            $name = $item['value'][0]['value'];
            $value = $item['value'][1]['value'];

            $currencyQuery = query()
                ->select('*')
                ->from('Currency')
                ->where('name = :name')
                ->setParameter('name', $name)
                ->execute()
                ->fetchAssociative();

            if ($currencyQuery->num_rows == 0 ) {
                query()
                    ->update('Currency')
                    ->set('value', ':value')
                    ->setParameters([
                        'value' => $value,
                    ])
                    ->where('name = :name')
                    ->setParameter('name', $name)
                    ->execute();

            } else {
                query()
                    ->insert('Currency')
                    ->values([
                        'name' => ':name',
                        'value' => ':value'
                    ])
                    ->setParameters([
                        'name' => $name,
                        'value' => $value,
                    ])
                    ->execute();
            }
        }

        $currencyQuery = query()
            ->select('*')
            ->from('Currency')
            ->execute()
            ->fetchAllAssociative();

        $currency = [];

        foreach ($currencyQuery as $currency) {
            echo $currency['Name'] . ' = ';
            echo $currency['value'] . ', ';
        }
    }
}