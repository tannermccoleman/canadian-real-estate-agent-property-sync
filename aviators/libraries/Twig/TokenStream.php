<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 * (c) 2009 Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a token stream.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class CreasyncTokenStream
{
    protected $tokens;
    protected $current;
    protected $filename;

    /**
     * Constructor.
     *
     * @param array  $tokens   An array of tokens
     * @param string $filename The name of the filename which tokens are associated with
     */
    public function __construct(array $tokens, $filename = null)
    {
        $this->tokens     = $tokens;
        $this->current    = 0;
        $this->filename   = $filename;
    }

    /**
     * Returns a string representation of the token stream.
     *
     * @return string
     */
    public function __toString()
    {
        return implode("\n", $this->tokens);
    }

    public function injectTokens(array $tokens)
    {
        $this->tokens = array_merge(array_slice($this->tokens, 0, $this->current), $tokens, array_slice($this->tokens, $this->current));
    }

    /**
     * Sets the pointer to the next token and returns the old one.
     *
     * @return CreasyncToken
     */
    public function next()
    {
        if (!isset($this->tokens[++$this->current])) {
            throw new CreasyncError_Syntax('Unexpected end of template', $this->tokens[$this->current - 1]->getLine(), $this->filename);
        }

        return $this->tokens[$this->current - 1];
    }

    /**
     * Tests a token and returns it or throws a syntax error.
     *
     * @return CreasyncToken
     */
    public function expect($type, $value = null, $message = null)
    {
        $token = $this->tokens[$this->current];
        if (!$token->test($type, $value)) {
            $line = $token->getLine();
            throw new CreasyncError_Syntax(sprintf('%sUnexpected token "%s" of value "%s" ("%s" expected%s)',
                $message ? $message.'. ' : '',
                CreasyncToken::typeToEnglish($token->getType(), $line), $token->getValue(),
                CreasyncToken::typeToEnglish($type, $line), $value ? sprintf(' with value "%s"', $value) : ''),
                $line,
                $this->filename
            );
        }
        $this->next();

        return $token;
    }

    /**
     * Looks at the next token.
     *
     * @param integer $number
     *
     * @return CreasyncToken
     */
    public function look($number = 1)
    {
        if (!isset($this->tokens[$this->current + $number])) {
            throw new CreasyncError_Syntax('Unexpected end of template', $this->tokens[$this->current + $number - 1]->getLine(), $this->filename);
        }

        return $this->tokens[$this->current + $number];
    }

    /**
     * Tests the current token
     *
     * @return bool
     */
    public function test($primary, $secondary = null)
    {
        return $this->tokens[$this->current]->test($primary, $secondary);
    }

    /**
     * Checks if end of stream was reached
     *
     * @return bool
     */
    public function isEOF()
    {
        return $this->tokens[$this->current]->getType() === CreasyncToken::EOF_TYPE;
    }

    /**
     * Gets the current token
     *
     * @return CreasyncToken
     */
    public function getCurrent()
    {
        return $this->tokens[$this->current];
    }

    /**
     * Gets the filename associated with this stream
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }
}
