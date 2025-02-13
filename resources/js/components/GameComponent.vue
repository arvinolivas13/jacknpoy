<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <div class="start" style="display: flex;" v-if="rounds === 0">
                    <div style="margin:auto;">
                        <h1 class="text-center">JACK N' POY</h1>
                        <div class="text-center mb-5">Rock, Paper, and Scissor</div>
                        <div class="your-score text-center mb-5">
                            Your Score: <span>{{ scores.you }}</span>
                            <div class="result" v-html="results"></div>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-primary" @click="rounds++; _d.match_logs=[];">START</button>
                            <br>
                            <button class="btn btn-link" data-bs-toggle="modal" data-bs-target="#historyModal">View Match History</button>
                        </div>
                    </div>
                </div>
                
                <div class="row" v-else>
                    <div class="col-md-12">
                        <h1 class="text-center">JACK N' POY</h1>
                    </div>
                    <div class="col-md-5">
                        <div class="img-display">
                            <img :src="`/img/${player1}-1.png`" width="100%"/>
                            <h5 class="text-center" style="font-weight: bold;">PLAYER 1 (COMPUTER)</h5>
                            <div class="points">
                                <span class="point-mark" v-for="item in scores.enemy" :key="item"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <h2 class="text-center">Round {{ rounds }}</h2>
                        <div style="display: flex;height: 100%;">
                            <h1 style="margin:auto;">VS</h1>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <img :src="`/img/${player2}-2.png`" width="100%"/>
                        <h5 class="text-center" style="font-weight: bold;text-decoration: underline;color: green;">PLAYER 2 (YOU)</h5>
                            <div class="points">
                                <span class="point-mark" v-for="item in scores.you" :key="item"></span>
                            </div>
                    </div>
                    <div class="col-md-12 mt-3 text-center">
                        <button class="options" v-for="(item, index) in move" :key="item" @click="selectMove(item, index)">
                            <img :src="`/img/${item}.png`" :alt="item" height="100%"/>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="historyModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Match History</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <th>Rounds</th>
                            <th>Results</th>
                        </thead>
                        <tbody v-if="match_logs.length !== 0">
                            <tr v-for="(item, index) in match_logs" :key="index" :class="`outcome-${item.outcome}`">
                                <td>Round {{ item.round }}</td>
                                <td>{{ item.outcome === 2?"Draw":(item.outcome === 0?"Player 1 Win":"You Win")}}</td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="2">No Records Found.</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-md-4">
                            <div>Player 1 Score:</div>
                            <div class="f-score">{{ scores.enemy }} - <span class="percentage">{{ ((scores.enemy)) * 10 }}%</span></div>
                        </div>
                        <div class="col-md-4">
                            <div>Player 2(You) Score:</div>
                            <div class="f-score">{{ scores.you }} - <span class="percentage">{{ ((scores.you)) * 10 }}%</span></div>
                        </div>
                        <div class="col-md-4">
                            <div>Total Draw rounds:</div>
                            <div class="f-score">{{ 10 - (scores.you + scores.enemy) }} - <span class="percentage">{{ (10 - (scores.you + scores.enemy)) * 10 }}%</span></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>
    </div>
    
</template>

<script>
    export default {
        data() {
            return {
                scores: {
                    you: 0,
                    enemy: 0
                },
                move: [ "rock", "paper", "scissor" ],
                player1: "rock",
                player2: "rock",
                rounds: 0,
                match_logs: [],
                results: ""
            }
        },
        mounted() {
            console.log('Component mounted.')
        },
        methods: {
            selectMove(att, ind) {
                var _d = this;

                _d.axios.post(`/get-outcome`, { id: ind, move: _d.move }).then((response) => {
                    _d.player1 = _d.move[response.data.computer_move];
                    _d.player2 = _d.move[response.data.your_move];

                    _d.match_logs.push({"round": _d.rounds, "outcome": response.data.outcome});

                    _d.scores.you += response.data.outcome === 1? 1:0;
                    _d.scores.enemy += response.data.outcome === 0? 1:0;

                    if(_d.rounds !== 10){
                        _d.rounds += 1;
                    }
                    else {
                        _d.rounds = 0;
                        _d.results = _d.scores.you === _d.scores.enemy?"<span class='draw'>Draw</span>":(_d.scores.you > _d.scores.enemy?"<span class='win'>You Win</span>":"<span class='lose'>You Lose</span>");
                        $('#historyModal').modal('show');
                    }
                });

            }
        }
    }
</script>

<style scoped>

.container, .container>.row, .container>.row>.col-md-12, .container>.row>.col-md-12>div.start {
    height: 100%;
}

button.options {
    height: 150px;
    width: 150px;
    margin: 10px;
    border-radius: 50%;
    border: 10px solid #3b2414;
}
span.point-mark {
    display: inline-block;
    vertical-align: top;
    width: 20px;
    height: 20px;
    margin: 3px;
    background: #ffe000;
    border-radius: 50%;
}
.points {
    text-align: center;
}

tr.outcome-1 {
    background: #c9ffc9;
}

tr.outcome-0 {
    background: #ffd1d1;
}
</style>