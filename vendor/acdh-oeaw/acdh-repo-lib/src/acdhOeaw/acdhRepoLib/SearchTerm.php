<?php

/*
 * The MIT License
 *
 * Copyright 2019 Austrian Centre for Digital Humanities.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace acdhOeaw\acdhRepoLib;

/**
 * Describes a single search condition.
 *
 * @author zozlak
 */
class SearchTerm {

    const TYPE_RELATION = 'relation';
    const TYPE_NUMBER   = 'number';
    const TYPE_DATE     = 'date';
    const TYPE_DATETIME = 'datetime';
    const TYPE_STRING   = 'string';

    /**
     * Property to be matched by the RDF triple.
     * 
     * @var string
     */
    public $property;

    /**
     * Operator to be used for the RDF triple value comparison.
     * 
     * One of `=`, `<`, `<=`, `>`, `>=`, `~` (regular expresion match), `@@` (full text search match)
     * 
     * @var string
     * @see $value
     */
    public $operator;

    /**
     * Value to be matched by the RDF triple (with a given operator)
     * 
     * @var mixed
     * @see $operator
     */
    public $value;

    /**
     * Data type to be matched by the RDF triple.
     * 
     * Should be one of main XSD data types or one of `TYPE_...` constants defined by this class.
     * 
     * @var string
     */
    public $type;

    /**
     * Language to be matched by the RDF triple
     * 
     * @var string
     */
    public $language;

    /**
     * Creates a search term object.
     * 
     * @param string|null $property property to be matched by the RDF triple
     * @param type $value value to be matched by the RDF triple (with a given operator)
     * @param string $operator operator used to compare the RDF triple value
     * @param string|null $type value to be matched by the RDF triple 
     *   (one of base XSD types or one of `TYPE_...` constants defined by this class)
     * @param string|null $language language to be matched by the RDF triple
     */
    public function __construct(?string $property = null, $value = null,
                                string $operator = '=', ?string $type = null,
                                ?string $language = null) {
        $this->property = $property;
        $this->operator = $operator;
        $this->value    = $value;
        $this->type     = $type;
        $this->language = $language;
    }

    /**
     * Returns the search term formatted as an HTTP query string.
     * 
     * @return string
     */
    public function getFormData(): string {
        $terms = [];
        foreach ($this as $k => $v) {
            if ($v !== null) {
                $terms[$k . '[]'] = (string) $v;
            }
        }
        return http_build_query($terms);
    }

}
