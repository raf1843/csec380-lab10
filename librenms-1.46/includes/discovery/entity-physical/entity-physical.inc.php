<?php
echo "\nCaching OIDs:";

if ($device['os'] == 'junos') {
    $entity_array = array();
    echo ' jnxBoxAnatomy';
    $entity_array = snmpwalk_cache_oid($device, 'jnxBoxAnatomy', $entity_array, 'JUNIPER-MIB');
} elseif ($device['os'] == 'timos') {
    $entity_array = array();
    echo 'tmnxHwObjs';
    $entity_array = snmpwalk_cache_multi_oid($device, 'tmnxHwObjs', $entity_array, 'TIMETRA-CHASSIS-MIB', '+'.$config['mib_dir'].'/aos:'.$config['mib_dir']);
} else {
    $entity_array = array();
    echo ' entPhysicalEntry';
    $entity_array = snmpwalk_cache_oid($device, 'entPhysicalEntry', $entity_array, 'ENTITY-MIB:CISCO-ENTITY-VENDORTYPE-OID-MIB');

    if (!empty($entity_array)) {
        echo ' entAliasMappingIdentifier';
        $entity_array = snmpwalk_cache_twopart_oid($device, 'entAliasMappingIdentifier', $entity_array, 'ENTITY-MIB:IF-MIB');
    }
}
if ($device['os'] == 'vrp') {
    echo ' hwEntityBoardType';
    $entity_array = snmpwalk_cache_oid($device, 'hwEntityBoardType', $entity_array, 'ENTITY-MIB:HUAWEI-ENTITY-EXTENT-MIB');
    echo ' hwEntityBomEnDesc';
    $entity_array = snmpwalk_cache_oid($device, 'hwEntityBomEnDesc', $entity_array, 'ENTITY-MIB:HUAWEI-ENTITY-EXTENT-MIB');
}

foreach ($entity_array as $entPhysicalIndex => $entry) {
    if ($device['os'] == 'junos') {
        // Juniper's MIB doesn't have the same objects as the Entity MIB, so some values
        // are made up here.
        $entPhysicalDescr        = $entry['jnxContentsDescr'];
        $entPhysicalContainedIn  = $entry['jnxContainersWithin'];
        $entPhysicalClass        = $entry['jnxBoxClass'];
        $entPhysicalName         = $entry['jnxOperatingDescr'];
        $entPhysicalSerialNum    = $entry['jnxContentsSerialNo'];
        $entPhysicalModelName    = $entry['jnxContentsPartNo'];
        $entPhysicalMfgName      = 'Juniper';
        $entPhysicalVendorType   = 'Juniper';
        $entPhysicalParentRelPos = -1;
        $entPhysicalHardwareRev  = $entry['jnxContentsRevision'];
        $entPhysicalFirmwareRev  = $entry['entPhysicalFirmwareRev'];
        $entPhysicalSoftwareRev  = $entry['entPhysicalSoftwareRev'];
        $entPhysicalIsFRU        = $entry['jnxFruType'];
        $entPhysicalAlias        = $entry['entPhysicalAlias'];
        $entPhysicalAssetID      = $entry['entPhysicalAssetID'];
        // fix for issue 1865, $entPhysicalIndex, as it contains a quad dotted number on newer Junipers
        // using str_replace to remove all dots should fix this even if it changes in future
        $entPhysicalIndex = str_replace('.', '', $entPhysicalIndex);
    } elseif ($device['os'] == 'timos') {
        $entPhysicalDescr        = $entry['tmnxCardTypeDescription'];
        $entPhysicalContainedIn  = $entry['tmnxHwContainedIn'];
        $entPhysicalClass        = $entry['tmnxHwClass'];
        $entPhysicalName         = $entry['tmnxCardTypeName'];
        $entPhysicalSerialNum    = $entry['tmnxHwSerialNumber'];
        $entPhysicalModelName    = $entry['tmnxHwMfgBoardNumber'];
        $entPhysicalMfgName      = $entry['tmnxHwMfgBoardNumber'];
        $entPhysicalVendorType   = $entry['tmnxCardTypeName'];
        $entPhysicalParentRelPos = $entry['tmnxHwParentRelPos'];
        $entPhysicalHardwareRev  = '1.0';
        $entPhysicalFirmwareRev  = $entry['tmnxHwBootCodeVersion'];
        $entPhysicalSoftwareRev  = $entry['tmnxHwBootCodeVersion'];
        $entPhysicalIsFRU        = $entry['tmnxHwIsFRU'];
        $entPhysicalAlias        = $entry['tmnxHwAlias'];
        $entPhysicalAssetID      = $entry['tmnxHwAssetID'];
        $entPhysicalIndex = str_replace('.', '', $entPhysicalIndex);
    } elseif ($device['os'] == 'vrp') {
        //Add some details collected in the VRP Entity Mib
        $entPhysicalDescr        = $entry['hwEntityBomEnDesc'];
        $entPhysicalContainedIn  = $entry['entPhysicalContainedIn'];
        $entPhysicalClass        = $entry['entPhysicalClass'];
        $entPhysicalName         = $entry['entPhysicalName'];
        $entPhysicalSerialNum    = $entry['entPhysicalSerialNum'];
        $entPhysicalModelName    = $entry['hwEntityBoardType'];
        $entPhysicalMfgName      = $entry['entPhysicalMfgName'];
        $entPhysicalVendorType   = $entry['entPhysicalVendorType'];
        $entPhysicalParentRelPos = $entry['entPhysicalParentRelPos'];
        $entPhysicalHardwareRev  = $entry['entPhysicalHardwareRev'];
        $entPhysicalFirmwareRev  = $entry['entPhysicalFirmwareRev'];
        $entPhysicalSoftwareRev  = $entry['entPhysicalSoftwareRev'];
        $entPhysicalIsFRU        = $entry['entPhysicalIsFRU'];
        $entPhysicalAlias        = $entry['entPhysicalAlias'];
        $entPhysicalAssetID      = $entry['entPhysicalAssetID'];
    } else {
        $entPhysicalDescr        = $entry['entPhysicalDescr'];
        $entPhysicalContainedIn  = $entry['entPhysicalContainedIn'];
        $entPhysicalClass        = $entry['entPhysicalClass'];
        $entPhysicalName         = $entry['entPhysicalName'];
        $entPhysicalSerialNum    = $entry['entPhysicalSerialNum'];
        $entPhysicalModelName    = $entry['entPhysicalModelName'];
        $entPhysicalMfgName      = $entry['entPhysicalMfgName'];
        $entPhysicalVendorType   = $entry['entPhysicalVendorType'];
        $entPhysicalParentRelPos = $entry['entPhysicalParentRelPos'];
        $entPhysicalHardwareRev  = $entry['entPhysicalHardwareRev'];
        $entPhysicalFirmwareRev  = $entry['entPhysicalFirmwareRev'];
        $entPhysicalSoftwareRev  = $entry['entPhysicalSoftwareRev'];
        $entPhysicalIsFRU        = $entry['entPhysicalIsFRU'];
        $entPhysicalAlias        = $entry['entPhysicalAlias'];
        $entPhysicalAssetID      = $entry['entPhysicalAssetID'];
    }//end if

    if (isset($entity_array[$entPhysicalIndex]['0']['entAliasMappingIdentifier'])) {
        $ifIndex = $entity_array[$entPhysicalIndex]['0']['entAliasMappingIdentifier'];
    }

    if (!strpos($ifIndex, 'fIndex') || $ifIndex == '') {
        unset($ifIndex);
    } else {
        $ifIndex_array = explode('.', $ifIndex);
        $ifIndex       = $ifIndex_array[1];
        unset($ifIndex_array);
    }

    // List of real names for cisco entities
    $entPhysicalVendorTypes = array(
        'cevC7xxxIo1feTxIsl'   => 'C7200-IO-FE-MII',
        'cevChassis7140Dualfe' => 'C7140-2FE',
        'cevChassis7204'       => 'C7204',
        'cevChassis7204Vxr'    => 'C7204VXR',
        'cevChassis7206'       => 'C7206',
        'cevChassis7206Vxr'    => 'C7206VXR',
        'cevCpu7200Npe200'     => 'NPE-200',
        'cevCpu7200Npe225'     => 'NPE-225',
        'cevCpu7200Npe300'     => 'NPE-300',
        'cevCpu7200Npe400'     => 'NPE-400',
        'cevCpu7200Npeg1'      => 'NPE-G1',
        'cevCpu7200Npeg2'      => 'NPE-G2',
        'cevPa1feTxIsl'        => 'PA-FE-TX-ISL',
        'cevPa2feTxI82543'     => 'PA-2FE-TX',
        'cevPa8e'              => 'PA-8E',
        'cevPaA8tX21'          => 'PA-8T-X21',
        'cevMGBIC1000BaseLX'   => '1000BaseLX GBIC',
        'cevPort10GigBaseLR'   => '10GigBaseLR',
    );

    if ($entPhysicalVendorTypes[$entPhysicalVendorType] && !$entPhysicalModelName) {
        $entPhysicalModelName = $entPhysicalVendorTypes[$entPhysicalVendorType];
    }

    // FIXME - dbFacile
    if ($entPhysicalDescr || $entPhysicalName) {
        $entPhysical_id = dbFetchCell('SELECT entPhysical_id FROM `entPhysical` WHERE device_id = ? AND entPhysicalIndex = ?', array($device['device_id'], $entPhysicalIndex));

        if ($entPhysical_id) {
            $update_data = array(
                'entPhysicalIndex'        => $entPhysicalIndex,
                'entPhysicalDescr'        => $entPhysicalDescr,
                'entPhysicalClass'        => $entPhysicalClass,
                'entPhysicalName'         => $entPhysicalName,
                'entPhysicalModelName'    => $entPhysicalModelName,
                'entPhysicalSerialNum'    => $entPhysicalSerialNum,
                'entPhysicalContainedIn'  => $entPhysicalContainedIn,
                'entPhysicalMfgName'      => $entPhysicalMfgName,
                'entPhysicalParentRelPos' => $entPhysicalParentRelPos,
                'entPhysicalVendorType'   => $entPhysicalVendorType,
                'entPhysicalHardwareRev'  => $entPhysicalHardwareRev,
                'entPhysicalFirmwareRev'  => $entPhysicalFirmwareRev,
                'entPhysicalSoftwareRev'  => $entPhysicalSoftwareRev,
                'entPhysicalIsFRU'        => $entPhysicalIsFRU,
                'entPhysicalAlias'        => $entPhysicalAlias,
                'entPhysicalAssetID'      => $entPhysicalAssetID,
            );
            dbUpdate($update_data, 'entPhysical', 'device_id=? AND entPhysicalIndex=?', array($device['device_id'], $entPhysicalIndex));
            echo '.';
        } else {
            $insert_data = array(
                'device_id'               => $device['device_id'],
                'entPhysicalIndex'        => $entPhysicalIndex,
                'entPhysicalDescr'        => $entPhysicalDescr,
                'entPhysicalClass'        => $entPhysicalClass,
                'entPhysicalName'         => $entPhysicalName,
                'entPhysicalModelName'    => $entPhysicalModelName,
                'entPhysicalSerialNum'    => $entPhysicalSerialNum,
                'entPhysicalContainedIn'  => $entPhysicalContainedIn,
                'entPhysicalMfgName'      => $entPhysicalMfgName,
                'entPhysicalParentRelPos' => $entPhysicalParentRelPos,
                'entPhysicalVendorType'   => $entPhysicalVendorType,
                'entPhysicalHardwareRev'  => $entPhysicalHardwareRev,
                'entPhysicalFirmwareRev'  => $entPhysicalFirmwareRev,
                'entPhysicalSoftwareRev'  => $entPhysicalSoftwareRev,
                'entPhysicalIsFRU'        => $entPhysicalIsFRU,
                'entPhysicalAlias'        => $entPhysicalAlias,
                'entPhysicalAssetID'      => $entPhysicalAssetID,
            );

            if (!empty($ifIndex)) {
                $insert_data['ifIndex'] = $ifIndex;
            }

            dbInsert($insert_data, 'entPhysical');
            echo '+';
        }//end if

        $valid[$entPhysicalIndex] = 1;
    }//end if
}//end foreach
echo "\n";
unset(
    $update_data,
    $insert_data,
    $entry,
    $entity_array
);
