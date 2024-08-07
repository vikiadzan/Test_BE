//Soal no 1
function reverseString(str) {
    
    const huruf = [];
    const angka = [];

    for (let i = 0; i < str.length; i++) {
        //cek string
        if (isNaN(str[i])) {
            huruf.push(str[i]);
        } else {
            angka.push({ index: i, value: str[i] });
        }
    }

    huruf.reverse();

    //memasukan kembali angka keposisi semula
    angka.forEach(num => {
        huruf.splice(num.index, 0, num.value);
    });
    
    const result = huruf.join('');
    console.log(result);
}

reverseString("NEGIE1")
reverseString("12AKU2")


//soal 2
function findLongestWord(str){
    var strSplit = str.split(' ')
    var longestWord = ''

    for(var i=0; i<strSplit.length; i++){
        //cek panjang string
        if(strSplit[i].length > longestWord.length){
            longestWord = strSplit[i]
        }
    }
    return `Kata terpanjang "${longestWord}" dengan panjang ${longestWord.length} karakter.`;
}

console.log(findLongestWord("Mari bekerja dengan giat karena kita merupakan pekerja keras lallalallalala"));


// soal 3
function cekArray(input,query) {
    const output = []

    //pengulangan array
    for(let q of query){
        let count = 0
        for(let i of input){
            // cek array yang sama
            if( i === q){
                count++
            }
        }
        output.push(count)
        console.log(`${q} terdapat ${count} kali di INPUT`);
    }
    
    return output
}

const INPUT = ['aa','cd','wew','qiw']
const QUERY = ['cd','re','qiw']

const OUTPUT = cekArray(INPUT,QUERY)
console.log(OUTPUT)



//soal 4
function penguranganMatrix(matrix) {
    let diagonal1 = 0;
    let diagonal2 = 0;
    let n = matrix.length;

    // Variabel untuk menyimpan nilai di dan d2
    let diagonal1Elements = [];
    let diagonal2Elements = [];

    //perulangan cek matrik
    for (let i = 0; i < n; i++) {
        diagonal1 += matrix[i][i];
        diagonal2 += matrix[i][n - 1 - i];

        diagonal1Elements.push(matrix[i][i]);
        diagonal2Elements.push(matrix[i][n - 1 - i]);
    }

    console.log(`Diagonal utama: ${diagonal1Elements.join(' + ')} = ${diagonal1}`);
    console.log(`Diagonal sekunder: ${diagonal2Elements.join(' + ')} = ${diagonal2}`);
    console.log(`${diagonal1} - ${diagonal2} = `);
    
    return diagonal1 - diagonal2;
}

const matrix = [
    [3, 4, 3, 3],
    [4, 1, 6, 3],
    [7, 1, 1, 5],
    [1, 4, 6, 1]
];

console.log(penguranganMatrix(matrix)); 
