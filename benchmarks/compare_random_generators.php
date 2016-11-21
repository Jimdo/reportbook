<?php

namespace Jimdo\Reports;

class CompareRandomGeneratorsBench
{
    /**
     * @Revs({10, 100, 1000})
     * @Iterations(5)
     */
    public function benchMCryptCreateIV()
    {
        mcrypt_create_iv(10);
    }

    /**
     * @Revs({10, 100, 1000})
     * @Iterations(5)
     */
    public function benchOpenSSLRandomPseudoBytes()
    {
        openssl_random_pseudo_bytes(10);
    }
}
