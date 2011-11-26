<?php

class PHPParser_Tests_codeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTestCode
     */
    public function testCode($name, $code, $dump) {
        $parser = new PHPParser_Parser;
        $dumper = new PHPParser_NodeDumper;

        $stmts = $parser->parse(new PHPParser_Lexer($code));
        $this->assertEquals(
            $this->canonicalize($dump),
            $this->canonicalize($dumper->dump($stmts)),
            $name
        );
    }

    public function provideTestCode() {
        $tests = array();

        $it = new RecursiveDirectoryIterator(dirname(__FILE__) . '/../../code');
        $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::LEAVES_ONLY);
        $it = new RegexIterator($it, '~\.test$~');

        foreach ($it as $file) {
            $fileContents = file_get_contents($file);

            // evaluate @@{expr}@@ expressions
            $fileContents = preg_replace('/@@\{(.*?)\}@@/e', '$1', $fileContents);

            $tests[] = array_map('trim', explode('-----', $fileContents));
        }

        return $tests;
    }

    protected function canonicalize($str) {
        // trim from both sides
        $str = trim($str);

        // normalize EOL to \n
        $str = str_replace(array("\r\n", "\r"), "\n", $str);

        // trim right side of all lines
        return implode("\n", array_map('rtrim', explode("\n", $str)));
    }
}