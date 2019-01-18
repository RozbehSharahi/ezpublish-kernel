<?php

/**
 * File containing a test class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace eZ\Publish\Core\REST\Server\Tests\Input\Parser\Criterion;

use eZ\Publish\API\Repository\Values\Content;
use eZ\Publish\Core\REST\Common\Input\ParsingDispatcher;
use eZ\Publish\Core\REST\Server\Input\Parser;
use eZ\Publish\Core\REST\Server\Tests\Input\Parser\BaseTest;

class LogicalOrTest extends BaseTest
{

    /**
     * Test parsing
     */
    public function testParseLogicalOr()
    {
        // This is what xml parser is right now creating out of
        // <OR>
        //   <ContentTypeIdentifierCriterion>author</ContentTypeIdentifierCriterion>
        //   <ContentTypeIdentifierCriterion>book</ContentTypeIdentifierCriterion>
        //   <Field>
        //     <name>title</name>
        //     <operator>EQ</operator>
        //     <value>Contributing to projects</value>
        //   </Field>
        //   <Field>
        //     <name>title</name>
        //     <operator>EQ</operator>
        //     <value>Contributing to projects</value>
        //   </Field>
        // </OR>
        $logicalAndParsedFromXml = [
            'OR' => [
                'ContentTypeIdentifierCriterion' => [
                    0 => 'author',
                    1 => 'book',
                ],
                'Field' => [
                    0 => [
                        'name' => 'title',
                        'operator' => 'EQ',
                        'value' => 'Contributing to projects'
                    ],
                    1 => [
                        'name' => 'title',
                        'operator' => 'EQ',
                        'value' => 'Contributing to projects'
                    ]
                ],
            ],
        ];

        $criterionMock = $this->createMock(Content\Query\Criterion::class);

        $parserMock = $this->createMock(\eZ\Publish\Core\REST\Common\Input\Parser::class);
        $parserMock->method('parse')->willReturn($criterionMock);

        $result = $this->internalGetParser()->parse($logicalAndParsedFromXml, new ParsingDispatcher([
            'application/vnd.ez.api.internal.criterion.ContentTypeIdentifier' => $parserMock,
            'application/vnd.ez.api.internal.criterion.Field' => $parserMock
        ]));

        self::assertInstanceOf(Content\Query\Criterion\LogicalOr::class, $result);
        self::assertCount(4, (array)$result->criteria);
    }

    /**
     * @return Parser\Criterion\LogicalOr
     */
    protected function internalGetParser()
    {
        return new Parser\Criterion\LogicalOr();
    }
}