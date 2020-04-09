<?php

declare(strict_types=1);

namespace App\UI\Webpack;

use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;

final class WebpackEncoreMacro extends MacroSet
{
	public static function install(Compiler $compiler): void
	{
		$static = new static($compiler);

		$static->addMacro('webpackJs', [$static, 'macroJs']);
		$static->addMacro('webpackCss', [$static, 'macroCss']);
	}

	public function macroJs(MacroNode $node, PhpWriter $writer): string
	{
		return $writer->write('echo $this->global->webpackEncoreTagRenderer->getJsAssets(%node.word);');
	}

	public function macroCss(MacroNode $node, PhpWriter $writer): string
	{
		return $writer->write('echo $this->global->webpackEncoreTagRenderer->getCssAssets(%node.word);');
	}
}
