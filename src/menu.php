<?php


$PLATFORM=[
  "path"=>"teprolin/complete",   // this is the main page
  "menu"=>[
      [
        "label"=>"TEPROLIN Service",
        "icon"=>"c-deep-purple-500 ti-comment-alt",
        "menu"=>[
            ["label"=>"Complete Flow", "path"=>"teprolin/complete"],
            ["label"=>"Custom Flow", "path"=>"teprolin/custom"],
            ["label"=>"Operations & Statistics", "path"=>"teprolin/stats"],
            ["label"=>"Developer Documentation", "path"=>"teprolin/doc_dev", "forceHTTP"=>true],
        ]
      ],

      [
        "label"=>"CoRoLa",
        "icon"=>"c-deep-purple-500 ti-comment-alt",
        "menu"=>[
            ["label"=>"About", "path"=>"corola/about", "forceHTTP"=>true],
            ["label"=>"Korap Search", "path"=>"corola/korap", "forceHTTP"=>true],
            ["label"=>"NL Search", "path"=>"corola/nlpcqp", "forceHTTP"=>true],
            ["label"=>"Audio Search", "path"=>"corola/ocqp", "forceHTTP"=>true],
            ["label"=>"Word Embeddings", "path"=>"corola/we", "forceHTTP"=>true],
            ["label"=>"Statistics", "path"=>"corola/stats", "forceHTTP"=>true],
        ]
      ],

      [
        "label"=>"RoWordNet",
        "icon"=>"c-deep-purple-500 ti-comment-alt",
        "menu"=>[
            ["label"=>"Query", "path"=>"rown/query"],
            ["label"=>"Aligned Query", "path"=>"rown/queryp"],
        ]
      ],      

      [
        "label"=>"Machine Translation",
        "icon"=>"c-deep-purple-500 ti-comment-alt",
        "menu"=>[
            ["label"=>"RO - EN", "path"=>"translate/ro_en"],
            ["label"=>"EN - RO", "path"=>"translate/en_ro"],
            ["label"=>"Developer Documentation", "path"=>"translate/dev_doc"],
        ]
      ],
      
      [
        "label"=>"ROBIN ASR",
        "icon"=>"c-deep-purple-500 ti-comment-alt",
        "menu"=>[
            ["label"=>"ASR", "path"=>"robin/asr"],
        ]
      ],

      [
        "label"=>"ROBIN TTS",
        "icon"=>"c-deep-purple-500 ti-comment-alt",
        "menu"=>[
            ["label"=>"TTS", "path"=>"robin/tts"],
        ]
      ],

      [
        "label"=>"Downloads",
        "icon"=>"c-deep-purple-500 ti-comment-alt",
        "rights"=>["user"],
        "menu"=>[
            ["label"=>"Internal", "path"=>"downloads/private","rights"=>["user"]],
        ]
      ],

      [
        "label"=>"Corpora",
        "icon"=>"c-deep-purple-500 ti-comment-alt",
        "rights"=>["corpus"],
        "menu"=>[
            ["label"=>"List", "path"=>"corpus/list","rights"=>["corpus"]],
        ]
      ],
      
      [
        "label"=>"Citation",
        "icon"=>"c-deep-purple-500 ti-comment-alt",
        "path"=>"papers/papers",
        "menu"=>[
            ["label"=>"Papers", "path"=>"papers/papers"],
        ]
      ],

      [
        "label"=>"MY",
        "icon"=>"c-deep-purple-500 ti-comment-alt",
        "rights"=>["pvf"],
        "menu"=>[
            ["label"=>"Authors Format", "path"=>"my/authors","rights"=>["pvf"]],
        ]
      ],

  ]    
];


?>