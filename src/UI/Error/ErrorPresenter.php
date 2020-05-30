<?php

declare(strict_types=1);

namespace App\UI\Error;

use Nette\Application\BadRequestException;
use Nette\Application\Helpers;
use Nette\Application\IResponse;
use Nette\Application\Request;
use Nette\Application\Responses;
use Nette\Application\UI\Presenter;
use Nette\Http;
use Nette\SmartObject;
use Tracy\ILogger;

final class ErrorPresenter extends Presenter
{
	use SmartObject;

	private ILogger $logger;

	public function __construct(ILogger $logger)
	{
		parent::__construct();
		$this->logger = $logger;
	}

	public function run(Request $request): IResponse
	{
		$exception = $request->getParameter('exception');

		if ($exception instanceof BadRequestException) {
			[$module, , $sep] = Helpers::splitName($request->getPresenterName());
			return new Responses\ForwardResponse($request->setPresenterName($module . $sep . 'Error4xx'));
		}

		$this->logger->log($exception, ILogger::EXCEPTION);
		return new Responses\CallbackResponse(function (
			Http\IRequest $httpRequest,
			Http\IResponse $httpResponse
		): void {
			$exception = preg_match('#^text/html(?:;|$)#', (string) $httpResponse->getHeader('Content-Type'));
			if ($exception === 1) {
				require __DIR__ . '/templates/500.phtml';
			}
		});
	}
}
