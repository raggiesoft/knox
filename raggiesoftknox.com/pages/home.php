<wa-layout-stack gap="5">

    <!-- ===== SECTION 1: HERO ===== -->
    <section class="hero-section" 
             aria-labelledby="hero-title" 
             style="background-image: url('https://assets.raggiesoft.com/images/aerie-hold-atmospheric-landscape.jpg');">
        
        <div class="hero-content">
            <h1 id="hero-title">KNOX</h1>
            <h2>On a planet of crushing gravity, they hunt a phantom. The reality is a family.</h2>
            <wa-button href="#core-conflict" variant="pact" size="large">
                <wa-icon name="fa-pro-solid fa-arrow-down-to-line"></wa-icon>
                Explore the Universe
            </wa-button>
        </div>
    </section>

    <!-- ===== SECTION 2: CORE CONFLICT (THE 3 DOORS) ===== -->
    <section id="core-conflict" class="page-container" aria-labelledby="core-conflict-title">
        
        <wa-layout-stack gap="2" align="center">
            <h2 id="core-conflict-title">One World. Three Truths.</h2>

            <wa-layout-grid cols="1" cols-md="3" gap="3">
                <wa-card aria-labelledby="pact-title">
                    <img slot="image" 
                         src="https://assets.raggiesoft.com/images/pip-fantasy.jpg" 
                         alt="Anya, Kael, and Pip cuddled together in their pod.">
                    
                    <h3 id="pact-title" slot="header">The Pact</h3>
                    <p>Follow the story of Anya, Kael, and Pip. From their hidden village, they wage a secret war of sabotage against the corporation that hunts them.</p>
                    
                    <div slot="footer">
                        <wa-button href="https://pact.raggiesoftknox.com/" variant="pact" full-width>
                            Read The Pact
                            <wa-icon name="fa-pro-solid fa-leaf" slot="suffix"></wa-icon>
                        </wa-button>
                    </div>
                </wa-card>

                <wa-card aria-labelledby="port-title">
                    <img slot="image" 
                         src="https://assets.raggiesoft.com/images/port-telsue-atmospheric.jpg" 
                         alt="The massive, industrial Axiom Spire of Port Telsus built on a coastal plateau.">
                    
                    <h3 id="port-title" slot="header">The Port</h3>
                    <p>Experience the narrative from inside the Axiom. Follow the Auditors, Agents, and Whispers trying to enforce corporate law in a Green Hell.</p>
                    
                    <div slot="footer">
                        <wa-button href="https://port.raggiesoftknox.com/" variant="axiom" full-width>
                            Enter The Port
                            <wa-icon name="fa-pro-solid fa-industry" slot="suffix"></wa-icon>
                        </button>
                    </div>
                </wa-card>

                <wa-card aria-labelledby="lore-title">
                    <img slot="image" 
                         src="https://assets.raggiesoft.com/images/aerie-hold-atmospheric.jpg" 
                         alt="The vertical village of Aerie-Hold, built into miles-high trees.">
                    
                    <h3 id="lore-title" slot="header">The Lore</h3>
                    <p>Explore the Lore Bible. The official codex for the factions, locations, and technology of Telsus Minor. Powered by WordPress.</p>
                    
                    <div slot="footer">
                        <wa-button href="https://lore.raggiesoftknox.com/" variant="neutral" full-width>
                            Open The Lore Bible
                            <wa-icon name="fa-pro-solid fa-book-journal-whills" slot="suffix"></wa-icon>
                        </button>
                    </div>
                </wa-card>
            </wa-layout-grid>
        </wa-layout-stack>
    </section>

    <!-- ===== SECTION 3: PREMISE ===== -->
    <section class="premise-section page-container" aria-labelledby="premise-title">
        <h2 id="premise-title">About the Knox Universe</h2>
        <p>On the oppressive, high-gravity jungle world of <strong>Telsus Minor</strong>, the monopolistic <strong>Axiom corporation</strong> rules through economic force. Their operations are plagued by a phantom saboteur they call <strong>"Knox"</strong>—a myth they believe to be a single, highly-trained ex-military operative.</p>
        <p>But the Axiom hunts a ghost of their own making. The reality is far more dangerous: <strong>Anya and Kael Rostova</strong>, young fraternal twins raised in the hidden canopy villages of "The Weave," wage a secret war using scavenged technology, improvised chemistry, and an intimate knowledge of the lethal environment the Axiom dismisses as the "Green Hell."</p>
    </section>

</wa-layout-stack>
