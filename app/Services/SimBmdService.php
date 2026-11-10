<?php

namespace App\Services;

/**
 * SimBmdService compatibility proxy for SipdBmdService.
 * Seamlessly transitions legacy SIMBMD calls to the updated SIPD e-BMD standard (Permendagri No. 108/2016).
 */
class SimBmdService extends SipdBmdService
{
    // Inherits all SIPD e-BMD methods: getKibCategories, getStandardCodes, searchCodes, determineKibType, getNextRegisterNumber, generateFullBmdCode
}
