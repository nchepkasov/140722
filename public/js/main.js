var getDomainsInfo = function (Domains, ExcludeActive) {
    
    $ ("#Loading").css ('visibility', 'visible');

    var PostData = {
        "Ajax": true,
        "action": "GetStatus", 
        "ExcludeActive": ExcludeActive,
        "Domains": Domains
    };

    $.post ("/", PostData, function (data) {
        data = JSON.parse (data);
        
        $ ("#AjaxContent").html ("");

        for (const errText in data ["Errors"]) {
            $ ("#AjaxContent").append (
                $ ("<p>").addClass ("text-warning").text (data ["Errors"][errText])
            );
        }
        
        if (Object.keys (data.Domains).length) {
            
            let table = $ ("<table>").addClass ("table table-bordered");
        
            for (let domain in data.Domains) {
                let heading = $ ("<tr>").addClass ("thead-light"),
                    no_results = data.Domains [domain] === null || (Array.isArray (data.Domains [domain]) && !data.Domains [domain].length);

                let rowspan = !no_results
                    ? Object.keys (data.Domains [domain]).length + 1
                    : 2 ;

                $ (heading).append (
                    $ ("<td>").attr ("rowspan", rowspan).html (
                        `<a target='_BLANK' href='//${domain}'>${domain}</a>`
                    )
                );

                $ (heading).append ($ ("<th>").text ("External domain"));
                $ (heading).append ($ ("<th>").text ("Status"));
                $ (heading).append ($ ("<th>").text ("DR"));

                $ (table).append (heading);

                if (no_results) {
                    $ (table).append (
                        $ ("<tr><td colspan='3'>No available domains</td></tr>")
                    );
                } else {
                    for (let external_domain in data.Domains [domain]) {
                        let domainRow = $ ("<tr>"), d = data.Domains [domain][external_domain];

                        let _2lvl = (d ['domain_to'] != d ['domain_to_orig'])
                            ? ` (RegDomain = ${d ['domain_to']})`
                            : ``;

                        $ (domainRow).append ($ ("<td>").html (`<a target='_BLANK' href='//${d ["domain_to_orig"]}'>${d ["domain_to_orig"]} ${_2lvl}</a>`));
                        $ (domainRow).append ($ ("<td>").html (`${d ["status"]}`));
                        $ (domainRow).append ($ ("<td>").html (`${d ["domain_to_rating"]}`));

                        $ (table).append (domainRow);
                    }
                }
            }

            $ ("#AjaxContent").append (table);
        }

        $ ("#Loading").css ("visibility", "hidden");
    });

};

$ (document).ready (function () {
    $ ('textarea').autogrow ({
        onInitialize: true
    });

    $ ("#GetDomainsInfo input[type='button']").click (function (e) {
        console.log ("here");

        getDomainsInfo (
            $ ("#Domains").val (),
            $ ("#ExcludeActive").is (':checked')
        );
        return false;
    });
});