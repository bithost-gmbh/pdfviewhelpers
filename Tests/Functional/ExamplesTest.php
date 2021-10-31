<?php

namespace Bithost\Pdfviewhelpers\Tests\Functional;

/* * *
 *
 * This file is part of the "PDF ViewHelpers" Extension for TYPO3 CMS.
 *
 *  (c) 2016 Markus Mächler <markus.maechler@bithost.ch>, Bithost GmbH
 *           Esteban Gehring <esteban.gehring@bithost.ch>, Bithost GmbH
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
 * @author Markus Mächler <markus.maechler@bithost.ch>, Esteban Gehring <esteban.gehring@bithost.ch>
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

        $this->assertStringContainsStringIgnoringCase('hallo@bithost.ch - www.bithost.ch', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('Full Feature Show Case', $pages[0]->getText());

        $this->assertStringContainsStringIgnoringCase('hallo@bithost.ch - www.bithost.ch', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('Application Development', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('This is a h1 headline', $pages[1]->getText());

        $this->assertStringContainsStringIgnoringCase('Only this page will have a different header', $pages[2]->getText());

        $this->assertStringContainsStringIgnoringCase('hallo@bithost.ch - www.bithost.ch', $pages[3]->getText());
        $this->assertStringContainsStringIgnoringCase('HTML content being styled externally', $pages[3]->getText());
        $this->assertStringContainsStringIgnoringCase('A Link to click', $pages[3]->getText());
        $this->assertStringContainsStringIgnoringCase('We are on page 4 of 5 pages.', $pages[3]->getText());

        $this->assertStringContainsStringIgnoringCase('Avoid page break inside', $pages[4]->getText());

        $this->validatePDF($output);

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
        $this->assertStringContainsStringIgnoringCase('28.03.2013', $text);
        $this->assertStringContainsStringIgnoringCase('Welcome to the extension pdfviewhelpers', $text);
        $this->assertStringContainsStringIgnoringCase('Some more information', $text);
        $this->assertStringContainsStringIgnoringCase('Lorem ipsum', $text);
        $this->assertStringContainsStringIgnoringCase('Esteban Gehring, Markus Mächler', $text);
        $this->assertStringContainsStringIgnoringCase('Bithost GmbH', $text);

        $this->validatePDF($output);

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
        $this->assertStringContainsStringIgnoringCase('Mega GmbH', $text);
        $this->assertStringContainsStringIgnoringCase('Ansprechpartner', $text);
        $this->assertStringContainsStringIgnoringCase('www.bithost.ch', $text);
        $this->assertStringContainsStringIgnoringCase('Here is your header', $text);
        $this->assertStringContainsStringIgnoringCase('Here is the HTML header', $text);
        $this->assertStringContainsStringIgnoringCase('Lorem ipsum dolor sit amet', $text);

        $this->validatePDF($output);

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

        $this->assertStringContainsStringIgnoringCase('HTML Table of content', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('html-table-of-content-header', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('html-table-of-content-footer', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('LEVEL 0: Headline page 1', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('LEVEL 0: Headline page 2', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('LEVEL 1: Headline page 2, level 1', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('LEVEL 0: Adding custom styled bookmark for a text', $pages[0]->getText());
        $this->assertStringContainsStringIgnoringCase('LEVEL 0: Adding custom styled ADVANCED bookmark for a text', $pages[0]->getText());
        $this->assertStringNotContainsStringIgnoringCase('Headline not in table of content', $pages[0]->getText());

        $this->assertStringContainsStringIgnoringCase('Regular table of content', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('regular-table-of-content-header', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('regular-table-of-content-footer', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('Headline page 1', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('Headline page 2', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('Headline page 2, level 1', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('Adding custom styled bookmark for a text', $pages[1]->getText());
        $this->assertStringContainsStringIgnoringCase('Adding custom styled ADVANCED bookmark for a text', $pages[1]->getText());
        $this->assertStringNotContainsStringIgnoringCase('LEVEL 0', $pages[1]->getText());
        $this->assertStringNotContainsStringIgnoringCase('Headline not in table of content', $pages[1]->getText());

        $this->assertStringContainsStringIgnoringCase('Headline page 1', $pages[2]->getText());
        $this->assertStringContainsStringIgnoringCase('General Header', $pages[2]->getText());
        $this->assertStringContainsStringIgnoringCase('General Footer', $pages[2]->getText());

        $this->assertStringContainsStringIgnoringCase('Headline page 2', $pages[3]->getText());
        $this->assertStringContainsStringIgnoringCase('Headline page 2, level 1', $pages[3]->getText());
        $this->assertStringContainsStringIgnoringCase('Headline page 2, level 2', $pages[3]->getText());

        $this->assertStringContainsStringIgnoringCase('Headline not in table of content', $pages[4]->getText());
        $this->assertStringContainsStringIgnoringCase('Here is some text', $pages[4]->getText());
        $this->assertStringContainsStringIgnoringCase('Headline not in table of content', $pages[4]->getText());

        //Do not validate as TCPDF does not produce valid documents having bookmarks
        //$this->validatePDF($output);
    }

    /**
     * @test
     */
    public function testPDFA()
    {
        $this->setUpPage([$this->getFixturePath('Examples/PDFA.txt')]);

        $output = $this->renderFluidTemplate($this->getFixturePath('Examples/PDFA.html'));

        $this->validatePDF($output);
    }

    /**
     * @param string $pdf
     */
    protected function validatePDF($pdf)
    {
        $client = new Client();
        $response = $client->post('https://www.pdf-online.com/osa/validate.aspx', [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'multipart' => [
                [
                    'name'     => 'file',
                    'contents' => $pdf,
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
