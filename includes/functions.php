<?php

function hcfw_get_replacements(){
    static $replace;

    if (is_array($replace)) {
        return $replace;
    }

    $replace = array();

    // Load user settings: Language
    $hcfw_language = get_option('hcfw_language');

    if(!empty($hcfw_language)) {
        switch($hcfw_language)
        {
            case ("enUS"):
                $json_lang = 'enUS';
                $data_hcfw_lang = 'enus';
                break;

            case ("frFR"):
                $json_lang = 'frFR';
                $data_hcfw_lang = 'frfr';
                break;

            case ("deDE"):
                $json_lang = 'deDE';
                $data_hcfw_lang = 'dede';
                break;

            case ("ruRU"):
                $json_lang = 'ruRU';
                $data_hcfw_lang = 'ruru';
                break;

            case ("esES"):
                $json_lang = 'esES';
                $data_hcfw_lang = 'eses';
                break;

            case ("ptBR"):
                $json_lang = 'ptBR';
                $data_hcfw_lang = 'ptbr';
                break;

            case ("zhCN"):
                $json_lang = 'zhCN';
                $data_hcfw_lang = 'zhcn';
                break;

            default:
                $json_lang = 'enUS';
                $data_hcfw_lang = 'enus';
                break;
        }
    } else {
        $json_lang = 'enUS';
        $data_hcfw_lang = 'enus';
    }

    // Card sizes
    $data_hcfw_width = '200';
    $data_hcfw_height = '303';

    // Load user settings: Customizations
    $hcfw_colored_card_names = get_option('hcfw_colored_card_names');

    $hcfw_bold_links = get_option('hcfw_bold_links');

    // Read json file
    $json_file = HCFW_PATH . 'includes/lib/AllSets.' . $json_lang . '.json';

    // Read json file
    $string = file_get_contents($json_file);

    if(isset($string) && !empty($string)) {

        // Prepare content
        $json_a = json_decode($string,true);

        foreach ($json_a as $key => $value){

            foreach($value as $sub) {

                if( isset( $sub['id'] ) && isset( $sub['name'] ) && isset( $sub['text'] ) ) {

                    // Setup classes
                    $classes = 'hcfw-card';

                    if($hcfw_colored_card_names == 1 && isset($sub['rarity'])) {
                        $classes .= ' hcfw-card-rarity-' . $sub['rarity'];
                    }

                    if($hcfw_bold_links == 1) {
                        $classes .= ' hcfw-bold';
                    }

                    // Setup link
                    $newName = '<a class="' . $classes . '" data-hcfw-card-id="' . $sub['id'] . '" data-hcfw-lang="'.$data_hcfw_lang.'" data-hcfw-width="'.$data_hcfw_width.'" data-hcfw-height="'.$data_hcfw_height.'" href="#" title="' . $sub['name'] . '">' . $sub['name'] . '</a>';

                    $replace['[' . $sub['name'] . ']'] = $newName;
                    $replace['[' . htmlentities($sub['name'], ENT_COMPAT, 'UTF-8') . ']'] = $newName;
                    $replace['[' . str_replace("'", '&#8217;', $sub['name']) . ']'] = $newName;

                    // Gold cards
                    $newNameGold = '<a class="' . $classes . '" data-hcfw-card-id="' . $sub['id'] . '" data-hcfw-lang="'.$data_hcfw_lang.'" data-hcfw-width="'.$data_hcfw_width.'" data-hcfw-height="'.$data_hcfw_height.'" href="#" title="' . $sub['name'] . '" data-hcfw-gold="true">' . $sub['name'] . '</a>';

                    $replace['[' . $sub['name'] . ' gold]'] = $newNameGold;
                    $replace['[' . htmlentities($sub['name'], ENT_COMPAT, 'UTF-8') . ' gold]'] = $newNameGold;
                    $replace['[' . str_replace("'", '&#8217;', $sub['name']) . ' gold]'] = $newNameGold;

                }
            }
        }
    }

    return $replace;
}

// Search & Replace Card Names
function hcfw_find_and_replace_cards($content){

    if( is_feed() )
        return $content;

    $replace = hcfw_get_replacements();

    $content = strtr($content, $replace);

    return $content;
}

// Filter
if(HCFW_ACTIVE) {
    add_filter('the_content', 'hcfw_find_and_replace_cards');
    add_filter('the_excerpt', 'hcfw_find_and_replace_cards');

    // Page Builder & Widgets
    add_filter('widget_text', 'hcfw_find_and_replace_cards');
    // Comments
    add_filter('comment_text', 'hcfw_find_and_replace_cards');
    // bbPress
    add_filter('bbp_get_topic_content', 'hcfw_find_and_replace_cards');
    add_filter('bbp_get_reply_content', 'hcfw_find_and_replace_cards');
    // BuddyPress
    add_filter('bp_get_activity_content_body', 'hcfw_find_and_replace_cards');
    add_filter('bp_get_the_thread_message_content', 'hcfw_find_and_replace_cards');
}