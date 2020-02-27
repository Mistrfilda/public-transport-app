<?php

declare(strict_types=1);

namespace App\UI\Admin\PragueDepartureTable\Form;

use App\Transport\Prague\DepartureTable\DepartureTableFacade;
use App\Transport\Prague\DepartureTable\DepartureTableRepository;
use App\Transport\Prague\Stop\StopRepository;
use App\UI\Admin\Base\AdminForm;
use App\UI\Admin\Base\AdminFormFactory;
use App\Utils\SelectPicker;
use Nette\Forms\Form;
use Ramsey\Uuid\UuidInterface;

class DepartureTableFormFactory
{
    /** @var DepartureTableRepository */
    private $departureTableRepository;

    /** @var DepartureTableFacade */
    private $departureTableFacade;

    /** @var StopRepository $stopRepository */
    private $stopRepository;

    /** @var AdminFormFactory */
    private $adminFormFactory;

    public function __construct(
        DepartureTableRepository $departureTableRepository,
        DepartureTableFacade $departureTableFacade,
        StopRepository $stopRepository,
        AdminFormFactory $adminFormFactory
    ) {
        $this->departureTableRepository = $departureTableRepository;
        $this->departureTableFacade = $departureTableFacade;
        $this->stopRepository = $stopRepository;
        $this->adminFormFactory = $adminFormFactory;
    }

    public function create(callable $onSuccess, ?UuidInterface $id = null): AdminForm
    {
        $form = $this->adminFormFactory->create(DepartureTableFormDTO::class);
        $form->ajax();

        $form->addSelect('stopId', 'Stop', $this->stopRepository->findPairs())
            ->setRequired()
            ->setPrompt(SelectPicker::PROMPT);

        $form->addInteger('numberOfFutureDays', 'Number of future days to download')
            ->setRequired()
            ->addRule(Form::RANGE, 'Please select value between %s and %s', [1, 15]);

        $form->onSuccess[] = function (AdminForm $form, $values) use ($onSuccess, $id): void {
            if ($id === null) {
                $this->createDepartureTable($form, $values, $onSuccess);
            } else {
                $this->updateDepartureTable($id, $form, $values, $onSuccess);
            }
        };

        if ($id !== null) {
            $this->setDefaults($id, $form);
        }

        $form->addSubmit('submit');

        return $form;
    }

    private function setDefaults(UuidInterface $id, AdminForm $form): void
    {
        $departureTable = $this->departureTableRepository->findById($id);
        $defaults = [
            'stopId' => $departureTable->getPragueStop()->getId(),
            'numberOfFutureDays' => $departureTable->getNumberOfFutureDays(),
        ];

        $form->setDefaults($defaults);
    }

    private function createDepartureTable(
        AdminForm $form,
        DepartureTableFormDTO $values,
        callable $onSuccess
    ): void {
        $this->departureTableFacade->createDepartureTable(
            $values->getStopId(),
            $values->getNumberOfFutureDays()
        );
        $onSuccess();
    }

    private function updateDepartureTable(
        UuidInterface $id,
        AdminForm $form,
        DepartureTableFormDTO $values,
        callable $onSucces
    ): void {
        $this->departureTableFacade->updateDepartureTable($id->toString(), $values->getNumberOfFutureDays());
        $onSucces();
    }
}
