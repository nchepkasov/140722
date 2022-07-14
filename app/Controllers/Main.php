<?php 

namespace App\Controllers;

use LayerShifter\TLDExtract\Extract;

class Main extends \App\Controller
{
    public function getIndexPage ()
    {
        return (new \App\BasicView ("index"))->render ();
    }
    
    public function getDomainsAjax ($List, $ExcludeActive)
    {           
        $TLDExtract = new Extract (null, null, Extract::MODE_ALLOW_ICANN);

        $Domains = [];

        $LinkedDomains = [];  $Errors = [];  $RegDomainList = [];

        /*****************************/

        set_time_limit (1200);

        $Domains       = array_filter (explode ("\n", str_replace ("\r", "", $List)));
        $ExcludeActive = filter_var ($ExcludeActive, FILTER_VALIDATE_BOOLEAN);
        
        $DomainsText   = htmlspecialchars ($List);

        /*****************************/

        $Ahrefs  = new \App\Services\AhrefsClient  (AHREFS_TOKEN);
        $Domainr = new \App\Services\DomainrClient (RAPIDAPI_TOKEN);
        
        foreach ($Domains as $Domain)
        {
            try
            {
                $LinkedDomains [$Domain] = $Ahrefs->GetLinkedDomains ($Domain, MAX_LINKED_DOMAINS);

                foreach ($LinkedDomains [$Domain] as &$d)
                {
                    $d ['domain_to_orig'] = $d ['domain_to'];
                }

                unset ($d);
            }
            catch (\Exception $e)
            {
                $LinkedDomains [$Domain] = null;

                $Errors [] = $Domain . ": " . $e->getMessage ();
                
                continue;
            }

            $LinkedDomains [$Domain] = array_combine (
                array_column ($LinkedDomains [$Domain], 'domain_to_orig'),
                $LinkedDomains [$Domain]
            );
            
            foreach (array_chunk ($LinkedDomains [$Domain], 10) as $Next10Domains)
                /* NC: Doesn't work for >10 domains at once */
            {
                foreach ($Next10Domains as $i => &$d)
                {
                    $SecondLevelDomain = $TLDExtract->parse ($d ['domain_to'])->getRegistrableDomain ();

                    if ($SecondLevelDomain == null)
                    {
                        unset ($LinkedDomains [$Domain][$d ['domain_to']]);
                        unset ($Next10Domains [$i]);
                    }

                    else if ($d ['domain_to'] != $SecondLevelDomain)
                    {
                        $LinkedDomains [$Domain][$d ['domain_to']]['domain_to'] = $SecondLevelDomain;

                        $d ['domain_to'] = $SecondLevelDomain;
                    }
                }

                unset ($d);

                try
                {
                    $DomainStatus = $Domainr->GetStatus (
                        array_column ($Next10Domains, 'domain_to')
                    );
                }
                catch (\Exception $e)
                {
                    $Errors [] = $e->getMessage ();

                    continue;
                }
                
                foreach ($DomainStatus as $ds)
                {
                    if ($ExcludeActive && in_array ('active', explode (' ', $ds ['status']))) 
                    {
                        foreach ($LinkedDomains [$Domain] as $i => $d)
                        {
                            if ($ds ['domain'] == $d ['domain_to'])
                            {
                                unset ($LinkedDomains [$Domain][$i]);
                            }
                        }
                        
                        continue;
                    }
                    
                    if (isset ($ds ['domain'])) 
                    {
                        foreach ($LinkedDomains [$Domain] as &$d)
                        {
                            if ($ds ['domain'] == $d ['domain_to'])
                            {
                                $d ['status'] = $ds ['status'];
                            }
                        }

                        unset ($d);
                    }
                }
            }
        }
        
        return json_encode ([
            'Errors' => $Errors,
            'Domains' => $LinkedDomains
            
            /*
                [
                    '123.com' => [ 
                        '1.com' => [ 'status' => 'active', 'domain_to' => 'test.com', 'domain_to_orig' => 'test2.com', 'domain_to_rating' => 111],
                        '2.com' => [ 'status' => 'active', 'domain_to' => 'test.com', 'domain_to_orig' => 'test2.com', 'domain_to_rating' => 111],
                        '3.com' => [ 'status' => 'active', 'domain_to' => 'test.com', 'domain_to_orig' => 'test2.com', 'domain_to_rating' => 111]
                    ],
                    '234.com' => [
                        '4.com' => [ 'status' => 'active', 'domain_to' => 'test.com', 'domain_to_orig' => 'test2.com', 'domain_to_rating' => 111],
                        '5.com' => [ 'status' => 'active', 'domain_to' => 'test.com', 'domain_to_orig' => 'test2.com', 'domain_to_rating' => 111],
                        '6.com' => [ 'status' => 'active', 'domain_to' => 'test.com', 'domain_to_orig' => 'test2.com', 'domain_to_rating' => 111],
                        '7.com' => [ 'status' => 'active', 'domain_to' => 'test.com', 'domain_to_orig' => 'test2.com', 'domain_to_rating' => 111],
                    ]
                ]
            */
        ]);
    }
}