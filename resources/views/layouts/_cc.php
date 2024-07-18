<div id="cc" class="fixed right-0 bottom-0 p-3 sm:p-5 w-full block-active">
    <div class="cc-content p-3 sm:p-5 rounded-lg text-xs block-active active">
        <p>
            Wir benötigen Ihre Zustimmung, um Cookies und Inhalte von Drittanbietern (ggf. auch aus dem EU-Ausland)
            einzubinden. Weitere Informationen in unserer <a href="/datenschutz">Datenschutzerklärung</a>.
        </p>
        <div class="border border-white mt-5 rounded-lg flex">
            <button class="p-3 w-full grow text-center text-xs cc-secondary cc-hover toggle" data-target=".cc-content">
                Anpassen
            </button>
            <button class="border-l border-white p-3 w-full grow text-center text-xs cc-secondary cc-hover cc-confirm" data-consent="none">
                Alle ablehnen
            </button>
            <button class="border-l border-white p-3 w-full grow text-center text-xs cc-hover cc-confirm" data-consent="analytics,external">
                Alle zustimmen
            </button>
        </div>
    </div>
    <div class="cc-content p-3 sm:p-5 rounded-lg block-active">
        <div class="cc-wrap flex flex-col">
            <div class="overflow-y-scroll">
                <div class="text-right">
                    <button class="text-sm toggle" data-target=".cc-content">zurück</button>
                </div>
                <div class="group pt-5">
                    <div class="flex justify-between">
                        <div class="flex items-center cursor-pointer toggle" data-target-closest=".group">
                            <div class="cc-arrow h-5 mr-1 aspect-square"></div>
                            <div class="leading-none text-sm">Technisch notwendige Cookies</div>
                        </div>
                        <label class="cc-label">
                            <input type="checkbox" class="cc-checkbox h-5 border border-white w-full relative" disabled data-consent="none" checked>
                        </label>
                    </div>
                    <div class="p-3 pl-0 text-xs block-group-active">
                        <p>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                            incididunt ut labore
                        </p>
                    </div>
                </div>
                <div class="group pt-3 mt-3 border-t border-white">
                    <div class="flex justify-between">
                        <div class="flex items-center cursor-pointer toggle" data-target-closest=".group">
                            <div class="cc-arrow h-5 mr-1 aspect-square"></div>
                            <div class="leading-none text-sm">Inhalte von Drittanbietern</div>
                        </div>
                        <label class="cc-label">
                            <input type="checkbox" class="cc-checkbox h-5 border border-white w-full cc-hover relative" data-consent="external">
                        </label>
                    </div>
                    <div class="p-3 pl-0 text-xs block-group-active">
                        <p>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                            incididunt ut labore
                        </p>
                    </div>
                </div>
                <div class="group pt-3 mt-3 border-t border-white">
                    <div class="flex justify-between">
                        <div class="flex items-center cursor-pointer toggle" data-target-closest=".group">
                            <div class="cc-arrow h-5 mr-1 aspect-square"></div>
                            <div class="leading-none text-sm">Statistiken</div>
                        </div>
                        <label class="cc-label">
                            <input type="checkbox" class="cc-checkbox h-5 border border-white w-full cc-hover relative" data-consent="analytics">
                        </label>
                    </div>
                    <div class="p-3 pl-0 text-xs block-group-active">
                        <p>
                            Statistik-Cookies helfen Webseiten-Besitzern zu verstehen, wie Besucher mit Webseiten
                            interagieren, indem Informationen anonym gesammelt und gemeldet werden.
                            Wenn Sie in die Statistik-Cookies einwilligen, werden ggf. personenbezogene Daten (z.B.
                            Ihre IP-Adresse) an Dritte außerhalb der EU weitergegeben. Sie willigen in diesem Fall
                            ausdrücklich ein.
                            Weitere Informationen, auch zu den Risiken und zur Widerrufsmöglichkeit, finden Sie in
                            unserer
                            <a href="/datenschutz">Datenschutzerklärung</a> hier.
                        </p>
                    </div>
                </div>
            </div>
            <div class="border border-white mt-5 rounded-lg flex">
                <button class="p-3 w-full grow text-center text-xs cc-hover cc-confirm">
                    Ausgewählte zustimmen
                </button>
                <button class="border-l border-white p-3 w-full grow text-center text-xs cc-hover cc-confirm"
                        data-consent="analytics,external">
                    Alle zustimmen
                </button>
            </div>
        </div>
    </div>
</div>