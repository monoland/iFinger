<template>
    <div class="monoland" :v-cloak="!fontLoaded">
        <v-app v-cloak>
            <v-app-bar color="light-blue" flat app>
                <div class="title white--text">Dashboard</div>
            </v-app-bar>
            <v-content class="light-blue lighten-5">
                <v-container>
                    <v-row>
                        <v-col cols="12">
                            <v-card flat>
                                <v-system-bar color="cyan">
                                    <div class="overline px-2 white--text">daftar apel</div>
                                </v-system-bar>
                                <v-card-text class="cyan lighten-4">
                                    <v-row no-gutters>
                                        <v-col class="pr-4" cols="11">
                                            <v-combobox
                                                :items="events"
                                                label="Pilih Event/Tanggal Apel"
                                                v-model="event"
                                            ></v-combobox>
                                        </v-col>

                                        <v-col class="d-flex align-center" cols="1">
                                            <v-btn color="cyan" dark @click="fetchRecaps">proses</v-btn>
                                        </v-col>
                                    </v-row>
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>

                    <v-row>
                        <!-- tepat waktu -->
                        <v-col cols="3">
                            <v-card flat>
                                <v-system-bar color="light-blue">
                                    <div class="overline px-2 white--text">hadir</div>
                                </v-system-bar>

                                <v-card-text class="light-blue lighten-2">
                                    <div class="d-flex justify-center display-4 font-weight-bold white--text">{{ count.present }}</div>
                                </v-card-text>
                                <v-card-text class="d-flex py-2 align-center light-blue lighten-3">
                                    <div class="body-2 text-uppercase font-weight-bold white--text">tepat waktu</div>
                                    <v-spacer></v-spacer>
                                    <div class="title white--text">{{ ((count.present / count.total) * 100).toFixed(2) + '%' }}</div>
                                </v-card-text>
                            </v-card>    
                        </v-col>

                        <!-- terlambat -->
                        <v-col cols="3">
                            <v-card flat>
                                <v-system-bar color="cyan">
                                    <div class="overline px-2 white--text">hadir</div>
                                </v-system-bar>

                                <v-card-text class="cyan lighten-2">
                                    <div class="d-flex justify-center display-4 font-weight-bold white--text">{{ count.late }}</div>
                                </v-card-text>
                                <v-card-text class="d-flex py-2 align-center cyan lighten-3">
                                    <div class="body-2 text-uppercase font-weight-bold white--text">terlambat</div>
                                    <v-spacer></v-spacer>
                                    <div class="title white--text">{{ ((count.late / count.total) * 100).toFixed(2) + '%' }}</div>
                                </v-card-text>
                            </v-card>    
                        </v-col>

                        <!-- dengan keterangan -->
                        <v-col cols="3">
                            <v-card flat>
                                <v-system-bar color="amber">
                                    <div class="overline px-2 white--text">tidak hadir</div>
                                </v-system-bar>

                                <v-card-text class="amber lighten-2">
                                    <div class="d-flex justify-center display-4 font-weight-bold white--text">{{ count.permit }}</div>
                                </v-card-text>
                                <v-card-text class="d-flex py-2 align-center amber lighten-3">
                                    <div class="body-2 text-uppercase font-weight-bold white--text">dengan keterangan</div>
                                    <v-spacer></v-spacer>
                                    <div class="title white--text">{{ ((count.permit / count.total) * 100).toFixed(2) + '%' }}</div>
                                </v-card-text>
                            </v-card>    
                        </v-col>

                        <!-- tanpa keterangan -->
                        <v-col cols="3">
                            <v-card flat>
                                <v-system-bar color="deep-orange">
                                    <div class="overline px-2 white--text">tidak hadir</div>
                                </v-system-bar>

                                <v-card-text class="deep-orange lighten-2">
                                    <div class="d-flex justify-center display-4 font-weight-bold white--text">{{ count.walkout }}</div>
                                </v-card-text>
                                <v-card-text class="d-flex py-2 align-center deep-orange lighten-3">
                                    <div class="body-2 text-uppercase font-weight-bold white--text">tanpa keterangan</div>
                                    <v-spacer></v-spacer>
                                    <div class="title white--text">{{ ((count.walkout / count.total) * 100).toFixed(2) + '%' }}</div>
                                </v-card-text>
                            </v-card>    
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col cols="6">
                            <v-card height="100%" flat>
                                <v-system-bar color="cyan">
                                    <div class="overline px-2 white--text">graphic kehadiran</div>
                                </v-system-bar>
                                <v-card-text class="cyan lighten-4" style="height: calc(100% - 24px);">
                                    <div class="d-flex justify-center overline" v-if="dataTable1.length === 0">tidak ada data untuk di display</div>
                                    <div class="d-flex align-center justify-center" id="graph-wrap" v-else style="position: relative; height: 100%;"></div>
                                </v-card-text>
                            </v-card>    
                        </v-col>

                        <v-col cols="6">
                            <v-card flat>
                                <v-system-bar color="cyan">
                                    <div class="overline px-2 white--text">tidak hadir dengan keterangan</div>
                                </v-system-bar>
                                <v-card-text class="cyan lighten-4">
                                    <div class="d-flex justify-center overline" v-if="dataTable1.length === 0">tidak ada data untuk di display</div>
                                    <v-data-table v-else
                                        :headers="headTable1"
                                        :items="dataTable1"
                                        :footer-props="{ itemsPerPageOptions: [-1] }"
                                        dense
                                        hide-default-footer
                                    >
                                        <template v-slot:item.percent="{ item }">
                                            {{ ((item.qty / count.total) * 100).toFixed(2) }}
                                        </template>
                                    </v-data-table>
                                </v-card-text>
                            </v-card>    
                        </v-col>
                    </v-row>
                </v-container>
            </v-content>

            <v-snackbar
                :color="snackbar.mode"
                v-model="snackbar.state"
            >
                {{ snackbar.message }}
                <v-btn text @click="snackbar.state = false">close</v-btn>    
            </v-snackbar>

            <v-footer color="light-blue" padless>
                <v-col cols="12" class="text-center">
                    <div class="overline white--text">&copy; 2019 - BKD - Provinsi Banten</div>
                </v-col>
            </v-footer>
        </v-app>
    </div>
</template>

<script>
import WebFontLoader from 'webfontloader';
import Chart from 'chart.js';

Chart.defaults.global.legend.display = false;

export default {
    name: 'mono-apps',

    created() {
        WebFontLoader.load({
            custom: {
                families: ['Roboto', 'Material Icons'],
                urls: ['/fonts/webfont.min.css']
            },
            active: this.setFontLoaded
        });
    },

    data:() => ({
        event: null,
        events: [],
        fontLoaded: false,
        urlpath: '/api',

        dataTable1: [],
        headTable1: [
            { text: 'Kode', value: 'stat_ijn', class: 'kode' },
            { text: 'Keterangan', value: 'namaijin' },
            { text: 'Jml', value: 'qty', align: 'end' },
            { text: '%', value: 'percent', align: 'end', sortable: false },
        ],

        snackbar: {
            state: false,
            mode: 'error',
            message: null
        },

        present: [],
        late: [],
        permit: [],
        walkout: [],

        count: {
            present: 0,
            late: 0,
            permit: 0,
            walkout: 0,
            total: 0
        }
    }),

    mounted() {
        this.fetchEvents();
    },

    methods: {
        fetchEvents: async function() {
            try {
                let { data } = await this.$http.get(this.urlpath + '/ceremony/events');

                this.events = data.events;
            } catch (error) {
                this.errorHandler(error);
            }
        },

        fetchRecaps: async function() {
            try {
                if (this.event === null) {
                    this.snackbar = {
                        mode: 'error',
                        message: 'Silahkan Pilih Event/Tanggal Apel',
                        state: true
                    };

                    return false;
                }

                let {data: { present, late, permit, walkout }} = await this.$http.get(this.urlpath + '/ceremony/' + this.event.value + '/recaps');

                this.present = present;
                this.late = late;
                this.permit = permit.data;
                this.walkout = walkout;

                this.count.present = this.present.length;
                this.count.late = this.late.length;
                this.count.permit = this.permit.length;
                this.count.walkout = this.walkout.length;
                this.count.total = this.count.present + this.count.late + this.count.permit + this.count.walkout;

                this.dataTable1 = permit.details;

                let chartdata = {
                    labels: ['Hadir Tepat Waktu', 'Hadir Terlambat', 'Tidak Hadir Dengan Keterangan', 'Tidak Hadir Tanpa Keterangan'],
                    datasets: [
                        {
                            data: [this.present.length, this.late.length, this.permit.length, this.walkout.length],
                            backgroundColor: ['#03A9F4', '#00BCD4', '#FFC107', '#FF5722']
                        }
                    ]
                };

                this.$nextTick(() => {
                    let wrp = document.getElementById('graph-wrap');
                        wrp.innerHTML = '';

                    let ctx = document.createElement('canvas');
                        ctx.setAttribute('width', wrp.offsetWidth - 24);
                        ctx.setAttribute('height', wrp.offsetWidth - 24);
                        wrp.appendChild(ctx);
                    
                    let txt = document.createElement('div');
                        txt.className = 'd-flex justify-center align-center display-4 font-weight-bold';
                        txt.style.position = 'absolute';
                        txt.style.top = 'calc(50% - 48px)';
                        txt.style.width = '250px';
                        txt.style.height = '96px';
                        txt.innerHTML = this.count.total;
                        wrp.appendChild(txt);
                    
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: chartdata
                    });
                });
                
            } catch (error) {
                this.errorHandler(error);
            }
        },

        errorHandler: function(error) {
            console.log(error);
        },

        setFontLoaded: function() {
            this.$nextTick(() => {
                this.fontLoaded = true;
            });
        }
    }
};
</script>