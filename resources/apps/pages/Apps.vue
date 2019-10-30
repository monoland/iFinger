<template>
    <div class="monoland" :v-cloak="!fontLoaded">
        <!-- mobile -->
        <v-app v-cloak v-if="$vuetify.breakpoint.xsOnly">
            <v-content>
                <v-card flat>
                    <v-toolbar color="light-blue" flat>
                        <v-toolbar-title class="font-weight-bold white--text">Dashboard</v-toolbar-title>
                    </v-toolbar>

                    <v-responsive :aspect-ratio="16/9" class="light-blue lighten-4">
                        <v-card-text class="pt-0 pl-4 pr-4 pb-8" style="height: 100%;">
                            <v-row align="center" justify="center" no-gutters style="height: 100%;">
                                <v-col cols="12">
                                    <div class="d-block">
                                        <v-combobox
                                            :items="events"
                                            label="Pilih Event/Tanggal Apel"
                                            v-model="event"
                                        ></v-combobox>
                                    </div>
                                    
                                    <div class="d-block">
                                        <v-btn block rounded large color="light-blue" dark @click="fetchRecaps">proses</v-btn>
                                    </div>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-responsive>

                    <v-container class="py-0 px-4" style="margin-top: -44px;">
                        <v-row>
                            <!-- tepat waktu -->
                            <v-col cols="6">
                                <v-hover>
                                    <template v-slot:default="{ hover }">
                                        <v-card flat>
                                            <v-card-text class="d-flex py-2 align-center light-blue">
                                                <div class="overline font-weight-bold text-truncate white--text">hadir</div>
                                            </v-card-text>

                                            <v-card-text class="light-blue lighten-2">
                                                <div class="d-flex justify-center display-3 white--text">{{ count.present }}</div>
                                            </v-card-text>

                                            <v-card-text class="d-flex py-2 align-center light-blue">
                                                <div class="overline font-weight-bold text-truncate white--text">tepat waktu</div>
                                            </v-card-text>

                                            <v-fade-transition>
                                                <v-overlay absolute color="light-blue" v-if="hover && count.total > 0">
                                                    <v-btn color="light-blue darken-3" dark @click="fetchDetail('present')">lihat detail</v-btn>
                                                </v-overlay>
                                            </v-fade-transition>
                                        </v-card>
                                    </template>
                                </v-hover>
                            </v-col>

                            <!-- terlambat -->
                            <v-col cols="6">
                                <v-hover>
                                    <template v-slot:default="{ hover }">
                                        <v-card flat>
                                            <v-card-text class="d-flex py-2 align-center cyan">
                                                <div class="overline font-weight-bold text-truncate white--text">hadir</div>
                                            </v-card-text>

                                            <v-card-text class="cyan lighten-2">
                                                <div class="d-flex justify-center display-3 white--text">{{ count.late }}</div>
                                            </v-card-text>

                                            <v-card-text class="d-flex py-2 align-center cyan">
                                                <div class="overline font-weight-bold text-truncate white--text">terlambat</div>
                                            </v-card-text>

                                            <v-fade-transition>
                                                <v-overlay absolute color="cyan" v-if="hover && count.total > 0">
                                                    <v-btn color="cyan darken-3" dark @click="fetchDetail('late')">lihat detail</v-btn>
                                                </v-overlay>
                                            </v-fade-transition>
                                        </v-card> 
                                    </template>
                                </v-hover>   
                            </v-col>

                            <!-- dengan keterangan -->
                            <v-col cols="6">
                                <v-hover>
                                    <template v-slot:default="{ hover }">
                                        <v-card flat>
                                            <v-card-text class="d-flex py-2 align-center amber">
                                                <div class="overline font-weight-bold text-truncate white--text">tidak hadir</div>
                                            </v-card-text>

                                            <v-card-text class="amber lighten-2">
                                                <div class="d-flex justify-center display-3 white--text">{{ count.permit }}</div>
                                            </v-card-text>

                                            <v-card-text class="d-flex py-2 align-center amber">
                                                <div class="overline font-weight-bold text-truncate white--text">dengan keterangan</div>
                                            </v-card-text>

                                            <v-fade-transition>
                                                <v-overlay absolute color="amber" v-if="hover && count.total > 0">
                                                    <v-btn color="amber darken-3" dark @click="fetchDetail('permit')">lihat detail</v-btn>
                                                </v-overlay>
                                            </v-fade-transition>
                                        </v-card>    
                                    </template>
                                </v-hover>
                            </v-col>

                            <!-- tanpa keterangan -->
                            <v-col cols="6">
                                <v-hover>
                                    <template v-slot:default="{ hover }">
                                        <v-card flat>
                                            <v-card-text class="d-flex py-2 align-center deep-orange">
                                                <div class="overline font-weight-bold text-truncate white--text">tidak hadir</div>
                                            </v-card-text>

                                            <v-card-text class="deep-orange lighten-2">
                                                <div class="d-flex justify-center display-3 white--text">{{ count.walkout }}</div>
                                            </v-card-text>

                                            <v-card-text class="d-flex py-2 align-center deep-orange">
                                                <div class="overline font-weight-bold text-truncate white--text">tanpa keterangan</div>
                                            </v-card-text>

                                            <v-fade-transition>
                                                <v-overlay absolute color="deep-orange" v-if="hover && count.total > 0">
                                                    <v-btn color="deep-orange darken-3" dark @click="fetchDetail('walkout')">lihat detail</v-btn>
                                                </v-overlay>
                                            </v-fade-transition>
                                        </v-card>    
                                    </template>
                                </v-hover>
                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col cols="12">
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
                        </v-row>
                    </v-container>
                </v-card>

                <!-- detail -->
                <v-dialog fullscreen transition="dialog-bottom-transition" v-model="detail.state">
                    <v-card :color="detail.color + ' lighten-5'" flat>
                        <v-toolbar :color="detail.color" flat dark>
                            <v-btn icon>
                                <v-icon @click="detail.state = false">close</v-icon>
                            </v-btn>
                            <v-toolbar-title class="white--text">{{ detail.title }}</v-toolbar-title>
                        </v-toolbar>

                        <v-list two-line subheader>
                            <template v-for="(record, index) in dataTable2">
                                <v-list-item
                                    :key="record.unker"
                                    @click="filterDetail(record.unker)"
                                >
                                    <v-list-item-avatar>
                                        <v-icon :class="detail.color + ' lighten-1 white--text'">folder</v-icon>
                                    </v-list-item-avatar>

                                    <v-list-item-content>
                                        <v-list-item-title v-text="record.unker"></v-list-item-title>
                                        <v-list-item-subtitle v-text="'Jumlah ASN : ' + record.count"></v-list-item-subtitle>
                                    </v-list-item-content>

                                    <v-list-item-action>
                                        <v-btn icon>
                                            <v-icon>keyboard_arrow_right</v-icon>
                                        </v-btn>
                                    </v-list-item-action>
                                </v-list-item>

                                <v-divider :key="index"></v-divider>
                            </template>
                        </v-list>
                    </v-card>
                </v-dialog>

                <!-- list ASN -->
                <v-dialog fullscreen transition="dialog-bottom-transition" v-model="lists.state">
                    <v-card :color="detail.color + ' lighten-5'" flat>
                        <v-toolbar :color="detail.color" flat dark>
                            <v-btn icon>
                                <v-icon @click="lists.state = false">arrow_back</v-icon>
                            </v-btn>
                            <v-toolbar-title class="white--text">{{ detail.title }}</v-toolbar-title>
                        </v-toolbar>

                        <v-list three-line subheader>
                            <v-subheader inset>{{ lists.title }}</v-subheader>

                            <template v-for="(asn, idx) in dataTable3">
                                <v-list-item :key="asn.nip">
                                    <v-list-item-avatar>
                                        <v-icon :class="detail.color + ' lighten-1 white--text'">folder</v-icon>
                                    </v-list-item-avatar>

                                    <v-list-item-content>
                                        <v-list-item-title v-text="asn.nama"></v-list-item-title>
                                        <v-list-item-subtitle>
                                            <div class="d-block">{{ 'NIP : ' + asn.nip }}</div>
                                            <div class="d-block">{{ 'Eselon : ' + asn.esl + ', Golongan : ' + asn.gol }}</div>
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>

                                <v-divider :key="idx"></v-divider>
                            </template>
                        </v-list>
                    </v-card>
                </v-dialog>
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

        <!-- desktop -->
        <v-app v-cloak v-else>
            <v-app-bar color="light-blue" flat app>
                <div class="title white--text">Dashboard</div>
                <v-spacer></v-spacer>
                <v-btn icon dark @click="printReport" v-if="dataTable1.length > 0">
                    <v-icon>print</v-icon>
                </v-btn>
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
                                        <v-col class="pr-4" sm="12" md="11">
                                            <v-combobox
                                                :items="events"
                                                label="Pilih Event/Tanggal Apel"
                                                v-model="event"
                                            ></v-combobox>
                                        </v-col>

                                        <v-col class="d-flex align-center" sm="12" md="1">
                                            <v-btn color="cyan" dark @click="fetchRecaps">proses</v-btn>
                                        </v-col>
                                    </v-row>
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>

                    <v-row>
                        <!-- tepat waktu -->
                        <v-col sm="12" md="3">
                            <v-hover>
                                <template v-slot:default="{ hover }">
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
                                            <div class="title white--text">{{ count.total > 0 ? ((count.present / count.total) * 100).toFixed(2) + '%' : '&nbsp;' }}</div>
                                        </v-card-text>

                                        <v-fade-transition>
                                            <v-overlay absolute color="light-blue" v-if="hover && count.total > 0">
                                                <v-btn color="light-blue darken-3" dark @click="fetchDetail('present')">lihat detail</v-btn>
                                            </v-overlay>
                                        </v-fade-transition>
                                    </v-card>
                                </template>
                            </v-hover>
                        </v-col>

                        <!-- terlambat -->
                        <v-col sm="12" md="3">
                            <v-hover>
                                <template v-slot:default="{ hover }">
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
                                            <div class="title white--text">{{ count.total > 0 ? ((count.late / count.total) * 100).toFixed(2) + '%' : '&nbsp;' }}</div>
                                        </v-card-text>

                                        <v-fade-transition>
                                            <v-overlay absolute color="cyan" v-if="hover && count.total > 0">
                                                <v-btn color="cyan darken-3" dark @click="fetchDetail('late')">lihat detail</v-btn>
                                            </v-overlay>
                                        </v-fade-transition>
                                    </v-card> 
                                </template>
                            </v-hover>   
                        </v-col>

                        <!-- dengan keterangan -->
                        <v-col sm="12" md="3">
                            <v-hover>
                                <template v-slot:default="{ hover }">
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
                                            <div class="title white--text">{{ count.total > 0 ? ((count.permit / count.total) * 100).toFixed(2) + '%' : '&nbsp;' }}</div>
                                        </v-card-text>

                                        <v-fade-transition>
                                            <v-overlay absolute color="amber" v-if="hover && count.total > 0">
                                                <v-btn color="amber darken-3" dark @click="fetchDetail('permit')">lihat detail</v-btn>
                                            </v-overlay>
                                        </v-fade-transition>
                                    </v-card>    
                                </template>
                            </v-hover>
                        </v-col>

                        <!-- tanpa keterangan -->
                        <v-col sm="12" md="3">
                            <v-hover>
                                <template v-slot:default="{ hover }">
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
                                            <div class="title white--text">{{ count.total > 0 ? ((count.walkout / count.total) * 100).toFixed(2) + '%' : '&nbsp;' }}</div>
                                        </v-card-text>

                                        <v-fade-transition>
                                            <v-overlay absolute color="deep-orange" v-if="hover && count.total > 0">
                                                <v-btn color="deep-orange darken-3" dark @click="fetchDetail('walkout')">lihat detail</v-btn>
                                            </v-overlay>
                                        </v-fade-transition>
                                    </v-card>    
                                </template>
                            </v-hover>
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col sm="12" md="6">
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

                        <v-col sm="12" md="6">
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

                <!-- detail -->
                <v-dialog fullscreen transition="dialog-bottom-transition" v-model="detail.state">
                    <v-card :color="detail.color + ' lighten-5'" flat>
                        <v-toolbar :color="detail.color" extended flat>
                            <v-btn icon @click="detail.state = false" dark>
                                <v-icon>close</v-icon>
                            </v-btn>
                        </v-toolbar>
                        <v-container class="pa-0">
                            <v-card class="mx-auto" style="margin-top: -64px" flat>
                                <v-toolbar :color="detail.color + ' darken-2'" flat>
                                    <v-toolbar-title class="text-uppercase white--text">{{ detail.title }}</v-toolbar-title>
                                </v-toolbar>
                                <v-divider></v-divider>
                                <v-card-text>
                                    <v-data-table
                                        :expanded.sync="expanded"
                                        :headers="headTable2"
                                        :items="dataTable2"
                                        item-key="unker"
                                        :footer-props="{ itemsPerPageOptions: [-1] }"
                                        dense 
                                        show-expand 
                                        single-expand
                                        hide-default-footer
                                    >
                                        <template v-slot:expanded-item="{ headers }">
                                            <td :colspan="headers.length" style="border-bottom: 0 none;">
                                                <v-data-table
                                                    :headers="headTable3"
                                                    :items="dataTable3"
                                                    :loading="waitTable3"
                                                    :footer-props="{ itemsPerPageOptions: [-1] }"
                                                    dense
                                                    hide-default-footer
                                                ></v-data-table>
                                            </td>
                                        </template>
                                    </v-data-table>
                                </v-card-text>
                            </v-card>
                        </v-container>
                    </v-card>
                </v-dialog>

                <!-- report -->
                <v-dialog fullscreen transition="dialog-bottom-transition" v-model="report.state">
                    <v-card color="light-blue lighten-5" flat>
                        <v-toolbar color="light-blue" extended flat>
                            <v-btn icon @click="report.state = false" dark>
                                <v-icon>close</v-icon>
                            </v-btn>
                        </v-toolbar>
                        <v-container class="pa-0">
                            <v-card class="mx-auto" style="margin-top: -64px" flat>
                                <v-toolbar color="light-blue darken-2" flat>
                                    <v-toolbar-title class="text-uppercase white--text">cetak laporan</v-toolbar-title>
                                    <v-spacer></v-spacer>
                                    <v-btn text @click="printPreview" dark>
                                        cetak
                                    </v-btn>
                                </v-toolbar>
                                <v-divider></v-divider>
                                <v-card-text id="print-area">
                                    <div class="print-page">
                                        <div class="print-title" style="margin-top: 72px; margin-bottom: 2px;">LAPORAN KEHADIRAN APEL TANGGAL {{ event ? event.value : null }}</div>
                                        <div style="margin-bottom: 72px; text-align: center;">Tanggal Cetak: {{ timing }}</div>
                                        
                                        <div id="chartImageHolder">
                                            <div class="print-total">{{ count.total }}</div>
                                        </div>
                                            
                                        <div class="print-subtitle">REKAPITULASI KEHADIRAN</div>
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="tbl-no">No</th>
                                                    <th>Keterangan</th>
                                                    <th class="tbl-right">ASN</th>
                                                    <th class="tbl-right">%</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>Hadir Tepat Waktu</td>
                                                    <td class="tbl-right">{{ count.present }}</td>
                                                    <td class="tbl-right">{{ count.total > 0 ? ((count.present / count.total) * 100).toFixed(2) + '%' : '&nbsp;' }}</td>
                                                </tr>

                                                <tr>
                                                    <td>2</td>
                                                    <td>Hadir Terlambat</td>
                                                    <td class="tbl-right">{{ count.late }}</td>
                                                    <td class="tbl-right">{{ count.total > 0 ? ((count.late / count.total) * 100).toFixed(2) + '%' : '&nbsp;' }}</td>
                                                </tr>

                                                <tr>
                                                    <td>3</td>
                                                    <td>Tidak Hadir Dengan Ijin</td>
                                                    <td class="tbl-right">{{ count.permit }}</td>
                                                    <td class="tbl-right">{{ count.total > 0 ? ((count.permit / count.total) * 100).toFixed(2) + '%' : '&nbsp;' }}</td>
                                                </tr>

                                                <tr>
                                                    <td>4</td>
                                                    <td>Tidak Hadir Tanpa Ijin</td>
                                                    <td class="tbl-right">{{ count.walkout }}</td>
                                                    <td class="tbl-right">{{ count.total > 0 ? ((count.walkout / count.total) * 100).toFixed(2) + '%' : '&nbsp;' }}</td>
                                                </tr>

                                                <tr>
                                                    <td colspan="2">TOTAL</td>
                                                    <td class="tbl-right">{{ count.total }}</td>
                                                    <td class="tbl-right">100%</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        
                                        <div class="print-subtitle">RINCIAN TIDAK HADIR DENGAN IJIN</div>
                                        
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="tbl-no">No</th>
                                                    <th>Keterangan</th>
                                                    <th class="tbl-right">ASN</th>
                                                    <th class="tbl-right">%</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <tr v-for="(record, index) in dataTable1" :key="index">
                                                    <td>{{ index + 1 }}</td>
                                                    <td>{{ record.namaijin }}</td>
                                                    <td class="tbl-right">{{ record.qty }}</td>
                                                    <td class="tbl-right">{{ ((record.qty / count.total) * 100).toFixed(2) }}</td>
                                                </tr>

                                                <tr>
                                                    <td colspan="2">TOTAL</td>
                                                    <td class="tbl-right">{{ count.permit }}</td>
                                                    <td class="tbl-right">{{ count.total > 0 ? ((count.permit / count.total) * 100).toFixed(2) + '%' : '&nbsp;' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="print-page">
                                        <div class="print-title">REKAPITULASI KEHADIRAN APEL TANGGAL {{ event ? event.value : null }}</div>

                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="tbl-no">No</th>
                                                    <th>Unit Kerja</th>
                                                    <th>Wajib</th>
                                                    <th class="tbl-right">Hadir</th>
                                                    <th class="tbl-right">Terlambat</th>
                                                    <th class="tbl-right">Ijin</th>
                                                    <th class="tbl-right">Tanpa Ijin</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <tr v-for="(record, index) in dataTable4" :key="index">
                                                    <td>{{ index + 1}}</td>
                                                    <td>{{ record.unit_kerja }}</td>
                                                    <td class="tbl-right">{{ record.wajib }}</td>
                                                    <td class="tbl-right">{{ record.hadir }}</td>
                                                    <td class="tbl-right">{{ record.telat }}</td>
                                                    <td class="tbl-right">{{ record.ijin }}</td>
                                                    <td class="tbl-right">{{ record.mangkir }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="print-page">
                                        <div class="print-title">DAFTAR PEGAWAI YANG TIDAK MENGIKUTI APEL TANGGAL {{ event ? event.value : null }}</div>

                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="tbl-no">No</th>
                                                    <th>NIP</th>
                                                    <th>Nama Lengkap</th>
                                                    <th>Gol</th>
                                                    <th class="tbl-esl">Esl</th>
                                                    <th>OPD</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <tr v-for="(record, index) in dataTable5" :key="index">
                                                    <td>{{ index + 1}}</td>
                                                    <td>{{ record.nip }}</td>
                                                    <td>{{ record.nama }}</td>
                                                    <td>{{ record.gol }}</td>
                                                    <td class="tbl-esl">{{ record.esl }}</td>
                                                    <td>{{ record.unker }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </v-card-text>
                            </v-card>
                        </v-container>
                    </v-card>
                </v-dialog>
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
        timing: null,
        events: [],
        fontLoaded: false,
        urlpath: '/dashboard/api',

        expanded: [],

        dataTable1: [],
        headTable1: [
            { text: 'Kode', value: 'stat_ijn', class: 'kode' },
            { text: 'Keterangan', value: 'namaijin' },
            { text: 'Jml', value: 'qty', align: 'end' },
            { text: '%', value: 'percent', align: 'end', sortable: false },
        ],

        dataTable2: [],
        headTable2: [
            { text: 'Unit Kerja', value: 'unker' },
            { text: 'Jumlah', value: 'count', align: 'end' },
            { text: '', value: 'data-table-expand' },
        ],

        holdTable3: [],
        dataTable3: [],
        waitTable3: false,
        headTable3: [
            { text: 'NIP', value: 'nip', class: 'nip' },
            { text: 'Nama Lengkap', value: 'nama' },
            { text: 'Eselon', value: 'esl', align: 'center', class: 'eselon' },
            { text: 'Golongan', value: 'gol', align: 'end', class: 'eselon' },
        ],

        dataTable4: [],
        dataTable5: [],

        snackbar: {
            state: false,
            mode: 'error',
            message: null
        },

        detail: {
            state: false,
            color: 'light-blue',
            title: null
        },

        lists: {
            state: false,
            title: null
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
        },

        report: {
            state: false,
        },

        expandOpen: 0,
        expandClose: 0,

        imageSrc: null
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

                let {data: { present, late, permit, walkout, recaps, timing }} = await this.$http.get(this.urlpath + '/ceremony/' + this.event.value + '/recaps');

                this.present = present;
                this.late = late;
                this.permit = permit.data;
                this.walkout = walkout;
                this.timing = timing;

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
                        ctx.setAttribute('id', 'chartCanvas');
                        ctx.setAttribute('width', wrp.offsetWidth - 24);
                        ctx.setAttribute('height', wrp.offsetWidth - 24);
                        wrp.appendChild(ctx);
                    
                    let txt = document.createElement('div');
                        txt.style.position = 'absolute';

                        if (this.$vuetify.breakpoint.xsOnly) {
                            txt.className = 'd-flex justify-center align-center display-2 font-weight-bold';
                            txt.style.top = 'calc(50% - 24px)';
                            txt.style.width = '145px';
                            txt.style.height = '48px';
                        } else {
                            txt.className = 'd-flex justify-center align-center display-4 font-weight-bold';
                            txt.style.top = 'calc(50% - 48px)';
                            txt.style.width = '250px';
                            txt.style.height = '96px';
                        }

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

        fetchDetail: async function(params) {
            let participants;

            switch (params) {
                case 'present':
                    this.detail.color = 'light-blue';  
                    this.detail.title = 'Hadir Tepat Waktu';  
                    participants = this.present;

                    break;
            
                case 'late':
                    this.detail.color = 'cyan';    
                    this.detail.title = 'Hadir Terlambat';
                    participants = this.late;

                    break;
                
                case 'permit':
                    this.detail.color = 'amber';    
                    this.detail.title = 'Tidak Hadir Dengan Keterangan';
                    participants = this.permit;

                    break;
                
                case 'walkout':
                    this.detail.color = 'deep-orange';    
                    this.detail.title = 'Tidak Hadir Tanpa Keterangan';
                    participants = this.walkout;

                    break;
            }

            let { data } = await this.$http.post( this.urlpath + '/ceremony/' + this.event.value + '/participants', {
                participants
            });

            this.dataTable2 = data.agencies;
            this.holdTable3 = data.participants;

            this.detail.state = true;
        },

        filterDetail: function(unker) {
            this.dataTable3 = [];

            let cloneData = Object.assign([], this.holdTable3);
            this.dataTable3 = this.holdTable3.reduce((current, record) => {
                if (record.unker === unker) {
                    current.push(record);
                }

                return current;
            }, []);

            this.lists.state = true;
            this.lists.title = unker;
        },

        errorHandler: function(error) {
            console.log(error);
        },

        setFontLoaded: function() {
            this.$nextTick(() => {
                this.fontLoaded = true;
            });
        },

        printReport: async function(){
            this.report.state = true;

            let { data: { agencies }} = await this.$http.get(this.urlpath + '/ceremony/' + this.event.value + '/agencies');

            this.dataTable4 = agencies;

            let { data: { walkouts }} = await this.$http.post( this.urlpath + '/ceremony/' + this.event.value + '/walkouts', {
                participants: this.walkout
            });

            this.dataTable5 = walkouts;

            this.$nextTick(() => {
                let canvas = document.getElementById('chartCanvas');
                let image = new Image();
                    image.src = canvas.toDataURL();

                let wrp = document.getElementById('chartImageHolder');
                    wrp.innerHTML = '';
                    wrp.appendChild('<div class="print-total">' + count.total + '</div>');
                    wrp.appendChild(image);
            });
        },

        printPreview: function() {
            let win = window.open('', 'PRINT', 'height=600,width=800');
                win.document.write('<html>');
                win.document.write('<head>');
                win.document.write('<title>Print Preview</title>');
                win.document.write('</head>');
                win.document.write('<body>');
                win.document.write(document.getElementById('print-area').innerHTML);
                win.document.write('</body>');
                win.document.write('</html>');

            let css = win.document.createElement('link');
                css.type = 'text/css';
                css.rel = 'stylesheet';
                css.href = '/dashboard/styles/monoland.css?version=1'; 
                css.media = 'all';
                win.document.getElementsByTagName("head")[0].appendChild(css);

            let prt = win.document.createElement('link');
                prt.type = 'text/css';
                prt.rel = 'stylesheet';
                prt.href = '/dashboard/styles/print.css'; 
                prt.media = 'all';
                win.document.getElementsByTagName("head")[0].appendChild(prt);

            setTimeout(() => {
                win.document.close();
                win.focus();
                win.print();
                win.close();
            }, 500);
        }
    },

    watch: {
        expanded: {
            handler: function(newValue) {
                if (newValue && newValue.constructor === Array && newValue.length > 0) {
                    this.dataTable3 = [];
                    this.waitTable3 = true;

                    let cloneData = Object.assign([], this.holdTable3);
                    this.dataTable3 = this.holdTable3.reduce((current, record) => {
                        if (record.unker === newValue[0].unker) {
                            current.push(record);
                        }

                        return current;
                    }, []);

                    this.waitTable3 = false;
                }
            },

            deep: true
        }
    }
};
</script>