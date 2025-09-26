// ==UserScript==
// @name         Expedition Uploader
// @namespace    http://tampermonkey.net/
// @version      0.3
// @description  Uploader for Moa Katangas expo statistics
// @author       Moa Katanga
// @match        https://s260-de.ogame.gameforge.com/game/index.php?page=ingame&component=messages
// @icon         https://www.google.com/s2/favicons?sz=64&domain=gameforge.com
// @grant        none
// ==/UserScript==

(function() {
    'use strict';

    console.log("Expedition uploader script loaded");

    var wait_for_element = function(selector, callback) {
        if ($(selector).length) {
            callback();
        }
        else
        {
            setTimeout(function() {
                wait_for_element(selector, callback);
            }, 100);
        }
    };

    var enter_expedition_tab = function() {

        setTimeout(function() {
            wait_for_element('.messagesHolder', function() {
                console.log("Expedition tab loaded");
                $('.nextPage, .previousPage').on("click", function() {
                    console.log("Navigation bar clicked");
                    enter_expedition_tab();
                });

                read_expeditions();
            });
        }, 2000);
    };

    var read_expeditions = function() {

        $('div.msg').each(function() {

            // Parse expedition report
            ////////////////////////////

            // Parse header
            var header = $(this).find($('.msgHead'));
            var title = header.find($('.msgTitle'));

            // Skip messages that are no expeditions
            if(!title.html().startsWith("Expeditionsergebnis")) {
                return;
            }

            // Find time of expedition
            var time = header.find($('.msgDate')).html();

            // Find user name
            var user = $('a.textBeefy').html();

            // Read message content
            var content = $(this).find($('.msgContent')).html().trim().replace(/<br>/g, "\n");

            var box = $(this).find($('.content-box'));
            if(box.length > 0)
            {
                console.log("There is a content box");
                content += "\nBeute\n";

                var row = box.find($('.loot-row'));
                let lootString = "";
                console.log(row);
                row.find($('.loot-item')).each(function() {
                    console.log("Loot detail gefunden");
                    let lootName = $(this).find('.loot-name').text().trim();
                    let amount = $(this).find('.amount').text().trim();

                    lootString += lootName + "\n" + amount + "\n";
                });
                //console.log(lootString);
                content += lootString;
            }

            // For debugging
            console.log(time);
            console.log(user);
            console.log(content);

            $.ajax({
                url: 'https://moakatanga.ddns.net/api/expo_stats.php',
                type: 'POST',
                data: {
                    user: user,
                    time: time,
                    report: content,
                    type: 'Expeditionsergebnis'
                },
                success: function(msg) {

                    var result = JSON.parse(msg);
		console.log(result);
                    if(result.status == 0)
                    {
                        let synced_span = $('<span style="color: green; margin-left: 10px;">synced</span>').insertAfter(title);
		    if("value" in result)
		        synced_span.append(' (value: ' + new Intl.NumberFormat('de-DE').format(result.value) + ')');
                    }
		else if(result.status == 1)
		{
		    $('<span style="color: yellow; margin-left: 10px;">duplicate expedition</span>').insertAfter(title);
		}
                    else
                    {
                        $('<span style="color: red; margin-left: 10px;">unknown error</span>').insertAfter(title);
                        console.log(result.message);
                    }
                },
                error: function(msg) {
                    $('<span style="color: red; margin-left: 10px;">sync failed</span>').insertAfter(title);
                    console.log(msg);
                }
            });

            return;
        });
    }

    // Main thread starts here
    wait_for_element('.messagesHolder', function() {
        $('.innerTabItem[data-subtab-id="22"]').on("click", enter_expedition_tab);
    });

})();
