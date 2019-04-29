<?php

namespace Bithost\Pdfviewhelpers\Tests\Functional;

/* * *
 *
 * This file is part of the "PDF ViewHelpers" Extension for TYPO3 CMS.
 *
 *  (c) 2016 Markus Mächler <markus.maechler@bithost.ch>, Bithost GmbH
 *           Esteban Marin <esteban.marin@bithost.ch>, Bithost GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * * */

use GuzzleHttp\Client;

/**
 * ExamplesTest
 *
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Marin <esteban.marin@bithost.ch>
 */
class ExamplesTest extends AbstractFunctionalTest
{
    /**
     * @test
     */
    public function testFullFeatureShowCase()
    {
        $this->setUpPage([$this->getFixturePath('Examples/FullFeatureShowCase.txt')]);

        $output = $this->renderFluidTemplate($this->getFixturePath('Examples/FullFeatureShowCase.html'));
        $pdf = $this->parseContent($output);
        $pages = $pdf->getPages();

        $this->assertEquals(5, count($pages));

        $this->assertContains('hallo@bithost.ch - www.bithost.ch', $pages[0]->getText());
        $this->assertContains('Full Feature Show Case', $pages[0]->getText());

        $this->assertContains('hallo@bithost.ch - www.bithost.ch', $pages[1]->getText());
        $this->assertContains('Application Development', $pages[1]->getText());
        $this->assertContains('This is a h1 headline', $pages[1]->getText());

        $this->assertContains('Only this page will have a different header', $pages[2]->getText());

        $this->assertContains('hallo@bithost.ch - www.bithost.ch', $pages[3]->getText());
        $this->assertContains('HTML content being styled externally', $pages[3]->getText());
        $this->assertContains('A Link to click', $pages[3]->getText());
        $this->assertContains('We are on page 4 of 5 pages.', $pages[3]->getText());

        $this->assertContains('Avoid page break inside', $pages[4]->getText());

        //do not assert file equality, because the files generated on the server are not equal to the local files
        //comparing hashes of two locally generated files works however
        //$expectedHash = sha1(file_get_contents($this->getFixturePath('Examples/FullFeatureShowCase.pdf')));
        //$actualHash = sha1($output);
        //$this->assertEquals($expectedHash, $actualHash);
    }

    /**
     * @test
     */
    public function testBasicUsage()
    {
        $this->setUpPage([$this->getFixturePath('Examples/BasicUsage.txt')]);

        $output = $this->renderFluidTemplate($this->getFixturePath('Examples/BasicUsage.html'));
        $pdf = $this->parseContent($output);
        $text = $pdf->getText();

        $this->assertEquals(1, count($pdf->getPages()));
        $this->assertContains('28.03.2013', $text);
        $this->assertContains('Welcome to the extension pdfviewhelpers', $text);
        $this->assertContains('Some more information', $text);
        $this->assertContains('Lorem ipsum', $text);
        $this->assertContains('Esteban Marín, Markus Mächler', $text);
        $this->assertContains('Bithost GmbH', $text);

        //$expectedHash = sha1(file_get_contents($this->getFixturePath('Examples/BasicUsage.pdf')));
        //$actualHash = sha1($output);
        //$this->assertEquals($expectedHash, $actualHash);
    }

    /**
     * @test
     */
    public function testExtendExistingPDFs()
    {
        $this->setUpPage([$this->getFixturePath('Examples/ExtendExistingPDFs.txt')]);

        $output = $this->renderFluidTemplate($this->getFixturePath('Examples/ExtendExistingPDFs.html'));
        $pdf = $this->parseContent($output);
        $text = $pdf->getText();

        $this->assertEquals(1, count($pdf->getPages()));
        $this->assertContains('Mega GmbH', $text);
        $this->assertContains('Ansprechpartner', $text);
        $this->assertContains('www.bithost.ch', $text);
        $this->assertContains('Here is your header', $text);
        $this->assertContains('Here is the HTML header', $text);
        $this->assertContains('Lorem ipsum dolor sit amet', $text);

        //$expectedHash = sha1(file_get_contents($this->getFixturePath('Examples/ExtendExistingPDFs.pdf')));
        //$actualHash = sha1($output);
        //$this->assertEquals($expectedHash, $actualHash);
    }

    /**
     * @test
     */
    public function testTableOfContent()
    {
        $this->setUpPage([$this->getFixturePath('Examples/TableOfContent.txt')]);

        $output = $this->renderFluidTemplate($this->getFixturePath('Examples/TableOfContent.html'));
        $pdf = $this->parseContent($output);
        $pages = $pdf->getPages();

        $this->assertCount(5, $pages);

        $this->assertContains('HTML Table of content', $pages[0]->getText());
        $this->assertContains('html-table-of-content-header', $pages[0]->getText());
        $this->assertContains('html-table-of-content-footer', $pages[0]->getText());
        $this->assertContains('LEVEL 0: Headline page 1', $pages[0]->getText());
        $this->assertContains('LEVEL 0: Headline page 2', $pages[0]->getText());
        $this->assertContains('LEVEL 1: Headline page 2, level 1', $pages[0]->getText());
        $this->assertContains('LEVEL 0: Adding custom styled bookmark for a text', $pages[0]->getText());
        $this->assertContains('LEVEL 0: Adding custom styled ADVANCED bookmark for a text', $pages[0]->getText());
        $this->assertNotContains('Headline not in table of content', $pages[0]->getText());

        $this->assertContains('Regular table of content', $pages[1]->getText());
        $this->assertContains('regular-table-of-content-header', $pages[1]->getText());
        $this->assertContains('regular-table-of-content-footer', $pages[1]->getText());
        $this->assertContains('Headline page 1', $pages[1]->getText());
        $this->assertContains('Headline page 2', $pages[1]->getText());
        $this->assertContains('Headline page 2, level 1', $pages[1]->getText());
        $this->assertContains('Adding custom styled bookmark for a text', $pages[1]->getText());
        $this->assertContains('Adding custom styled ADVANCED bookmark for a text', $pages[1]->getText());
        $this->assertNotContains('LEVEL 0', $pages[1]->getText());
        $this->assertNotContains('LEVEL 1', $pages[1]->getText());
        $this->assertNotContains('Headline not in table of content', $pages[1]->getText());

        $this->assertContains('Headline page 1', $pages[2]->getText());
        $this->assertContains('General Header', $pages[2]->getText());
        $this->assertContains('General Footer', $pages[2]->getText());

        $this->assertContains('Headline page 2', $pages[3]->getText());
        $this->assertContains('Headline page 2, level 1', $pages[3]->getText());
        $this->assertContains('Headline page 2, level 2', $pages[3]->getText());

        $this->assertContains('Headline not in table of content', $pages[4]->getText());
        $this->assertContains('Here is some text', $pages[4]->getText());
        $this->assertContains('Headline not in table of content', $pages[4]->getText());
    }

    /**
     * @test
     */
    public function testFullFeatureShowCaseValidity()
    {
        $this->setUpPage([$this->getFixturePath('Examples/FullFeatureShowCase.txt')]);

        $output = $this->renderFluidTemplate($this->getFixturePath('Examples/FullFeatureShowCase.html'));
        $client = new Client();
        $response = $client->post('https://www.pdf-online.com/osa/validate.aspx', [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'multipart' => [
                [
                    'name'     => 'file',
                    'contents' => $output,
                    'headers' => [
                        'Content-Disposition' => 'form-data; name="file"; filename="document.pdf"',
                        'Content-Type' => 'application/pdf',
                    ]
                ]
            ]
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('Result', $responseData);
        $this->assertArrayHasKey('Details', $responseData);
        $this->assertEquals(
            'Document validated successfully.',
            $responseData['Result'],
            implode(PHP_EOL, $responseData['Details'])
        );
    }
}
