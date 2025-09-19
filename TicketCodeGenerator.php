<?php

/**
 * TicketCodeGenerator
 *
 */
class TicketCodeGenerator {

    /**
     * The secret salt used to generate the codes.
     */
    private string $secretSalt = 'DE86B80C-5A89-43FB-A302-47658017E30E';

    /**
     * The characters used to compose the codes.
     */
    private string $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

    /**
     * The maximum number that can be used to generate a code.
     */
    private int|string $maxNumber = 50000;

    /**
     * The desirable length of the generated ticket codes.
     */
    private int $codeLength = 8; 

    /**
     * Generates a ticket code for the given ticket number.
     * 
     * @param int $ticketNumber The ticket number to generate a code for. A number between 0 and $maxNumber.
     * 
     * @return string The generated ticket code.
     */
    public function generate( $ticketNumber ) {

        if( $ticketNumber > $this->maxNumber || $ticketNumber <= 0 ){
            throw new \Exception( 'Ticket number must be between 1 and ' . $this->maxNumber );
        }
        $charsLen = strlen($this->characters);

        // Make hash of ticket number with the salt
        $hash = hash('sha256', $ticketNumber . $this->secretSalt);
        
        // Convert the hash into a number (using a portion of the hash for simplicity)
        $num = hexdec(substr($hash, 0, 10));
        
        // Encode the $num in custom chars ($this->characters)
        $code = '';
        while ($num > 0) {
            $code = $this->characters[$num % $charsLen] . $code;
            $num = (int)($num / $charsLen);
        }

        // Extend the code to $codeLength characters if it's shorter
        if( strlen($code) < $this->codeLength ){
            $code = str_pad($code, $this->codeLength, $this->codeLength, STR_PAD_LEFT);
        } else {
            $code = substr($code, 0, $this->codeLength);
        }

        return $code;
    }

    /**
     * Decodes a ticket code and returns the corresponding ticket number.
     * 
     * @param string $code The ticket code to decode.
     */
    public function decode($code) {
        // Iterate through possible ticket numbers and see which one matches. 
        for ($ticketNumber = 1; $ticketNumber <= $this->maxNumber; $ticketNumber++) {
            if ( $this->generate($ticketNumber) === $code ) {
                return $ticketNumber; // Match found
            }
        }

        return null;
    }

    public function setMaxNumber( $maxNumber ){
        $this->maxNumber = $maxNumber;
        return $this;
    }
}



$ticketNumber = $_GET['n'] ?? 1200;

// Example decoding:
$generator = new TicketCodeGenerator;
$code = $generator->setMaxNumber(60000)->generate($ticketNumber);

echo "Generated code for ticket #{$ticketNumber}: " . $code . "\n";
echo "Decoded ticket number from code: " . $generator->decode($code) . "\n";




