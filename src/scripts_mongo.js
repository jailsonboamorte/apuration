db.getCollection('map_votes_combinations_10_participants').mapReduce(
        function () {
            emit(this.winner, 1);
        },
        function (key, values) {
            return Array.sum(values)
        },
        {
            out: "count_groups_"
        }
)


db.system.js.deleteOne({_id: 'generateMapVotesCombinations'});
db.system.js.save({
    _id: "generateMapVotesCombinations",
    value: function (tableVotes, idVotes, idApuration,tableCombination, tableMapVotesCombination) {
        var cursorCombinations = db.getCollection(tableCombination).find({});
        var votes = db.getCollection(tableVotes).findOne({'_id': ObjectId(idVotes)}, {votes: 1});
        while (cursorCombinations.hasNext()) {

            var cursor = cursorCombinations.next();
            var position_0 = cursor.position_0;
            var position_1 = cursor.position_1;
            var position_2 = cursor.position_2;
            var votesCombination = [
                getVote(votes.votes, position_0),
                getVote(votes.votes, position_1),
                getVote(votes.votes, position_2),
            ]
            var sumVotes = sumVotesCombination(votesCombination);
            if (sumVotes['A'] > sumVotes['B']) {
                qty_votes_winner = sumVotes['A'];
                winner = 'A';
            } else {
                qty_votes_winner = sumVotes['B'];
                winner = 'B';
            }


            db.getCollection(tableMapVotesCombination).insert({
                apuration_id: idApuration,
                votes: votesCombination,                
                winner: winner,
                qty_votes_winner: qty_votes_winner
            })
        }
    }
});
db.loadServerScripts();
generateMapVotesCombinations('10_votes','5cc9b1354ca83f0038235fd2', 1,'combinations_for_10_participants','map_votes_combinations_10_participants');

generateMapVotesCombinations('1000_votes','"5cbe37b3d25bf2026b1edf52"', 1,'combinations_for_1000_participants','map_votes_combinations_1000_participants');

db.getCollection('combinations_for_10_participants').find({}).forEach(function (elem)
{
    var value_0 = elem['0'];
    var value_1 = elem['1'];
    var value_2 = elem['2'];
    db.getCollection('combinations_for_10_participants').update({_id: elem._id}, {$set: {position_0: NumberInt(value_0), position_1: NumberInt(value_1), position_2: NumberInt(value_2)}});
});



db.system.js.deleteOne({_id: 'reduceApuration'});
db.system.js.save({
    _id: "reduceApuration",
    value: function (source, target) {
        db.getCollection(source).mapReduce(
                function () {
                    emit(this.winner, NumberInt(1));
                },
                function (key, values) {
                    return Array.sum(values);
                },
                {out: target}
        )
    }
});
db.loadServerScripts();

reduceApuration('map_votes_combinations_10_participants', 'reduce_apuration_');
db.getCollection('reduce_apuration_').find();



db.system.js.deleteOne({_id: 'sumVotesCombination'});
db.system.js.save({
    _id: "sumVotesCombination",
    value: function (votes) {
        var sum_votes = [];
        sum_votes['A'] = sum_votes['B'] = NumberInt(0);

        for (i = 0; i < votes.length; i++) {
            option = votes[i];
            if (typeof sum_votes[option] === 'undefined') {
                sum_votes[option] = 1;
            } else {
                sum_votes[option] = NumberInt(sum_votes[option] + 1);
            }
        }

        return sum_votes;
    }
});
db.loadServerScripts();

var sum = sumVotesCombination(['A', 'A', 'A']);
//print(sum);


db.system.js.deleteOne({_id: 'createPositionsField'});
db.system.js.save({
    _id: "createPositionsField",
    value: function (colletion) {
        db.getCollection(colletion).find({}).forEach(function (elem)
        {
            var value_0 = elem['0'];
            var value_1 = elem['1'];
            var value_2 = elem['2'];
            db.getCollection(colletion).update({_id: elem._id}, {$set: {position_0: NumberInt(value_0), position_1: NumberInt(value_1), position_2: NumberInt(value_2)}});
        });
    }
});
db.loadServerScripts();

createPositionsField('combinations_for_100_participants');


db.system.js.deleteOne({_id: 'getVote'});
db.system.js.save({
    _id: "getVote",
    value: function (votes,position) {
        return votes[position];
    }
});