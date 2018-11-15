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
        random_bytes(10);
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
