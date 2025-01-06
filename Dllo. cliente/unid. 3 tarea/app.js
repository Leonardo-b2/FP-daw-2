"use strict"
//EJERCICIO 1
// let busqueda="The";

// let filtro_titulo=tienda.filter(
//     (disco)=>disco["titulo"].toLocaleLowerCase().includes(busqueda.toLocaleLowerCase())
// );
// console.log(filtro_titulo);

// let pais="ESP";
// let filtro_pais=tienda.filter((disco)=>disco["pais"]===pais)
// .sort((a,b)=>a["copias"]-b["copias"]);
// console.log(filtro_pais);

// tienda.sort((a,b)=>b["publicacion"]-a["publicacion"]);
// console.log(tienda[0]);

// tienda.sort((a,b)=>a["copias"]-b["copias"]);
// console.log(tienda[0]);

// let paises=[];
// tienda.forEach((disco,pos) => {
//     //console.log(disco,pos);
//     if(!paises.includes(disco["pais"])){
//         paises.push(disco["pais"]);       
//     }
// });
// console.log(paises);


// paises=tienda.filter(
//     (disco,posicion)=>tienda.findIndex((item=>item["pais"]===disco["pais"]))===posicion);
// console.log(paises);

 
// let numeros=[1,2,3,5,6,7,8,6,9,7,2];

// let sinrepes=numeros.filter(
//                 (numero,posicion)=>numeros.indexOf(numero)===posicion);
// console.log(sinrepes);


// let recaudacion={};//NOMBRE DEL PAIS ES CLAVE
// tienda.forEach(disco=>{
//     recaudacion[disco["pais"]]??=0;
//     recaudacion[disco["pais"]]+=disco["copias"]*1000*disco["precio"];
// })

// console.log(recaudacion);



//EJERCICIO 2
// Buscar un plato a partir del nombre (suponemos que el usuario lo ha escrito en una variable)
let nombre = "Tostada Francesa";
let plato = menu.find(plato => plato.nombre === nombre);
console.log(plato);

//Filtrar los platos que con mayor número de calorías que el usuario indique
let caloriasBase = 900;
let platos = menu.filter(plato => plato.calorias > caloriasBase);
console.log(platos);

//Mostrar el plato del menú con menor número de calorías.
let menorCalorias = menu.sort((a, b) => a.calorias - b.calorias)[0];
console.log(menorCalorias);

//Obtener el nombre y el precio del plato más caro.
let platoMasCaro = menu.sort((a,b) => b.precio - a.precio)[0];
console.log(platoMasCaro);


/*Obtener la suma de calorías según el tipo de plato de la carta (desayuno, almuerzo, cena), 
vuestro tiene que funcionar si se añaden o quitan tipos de platos de la carta.*/
const carta = menu
  .map(item => item.carta)
  .filter((carta, index, self) => self.indexOf(carta) === index);

console.log(carta); // ["desayuno", "almuerzo", "cena"]






//EJERCICIO 3
// let asignaturas={
//     "Matematicas":60,
//     "Fisica":45,
//     "Quimica":53
// }

// // let suma=0;
// // for(let nombre in asignaturas){
// //     console.log(nombre);
// //     suma+=asignaturas[nombre];
// // }
// // console.log(suma);

// // console.log(Object.keys(asignaturas));
// // console.log(Object.values(asignaturas));
// // console.log(Object.entries(asignaturas));



// let nombres=Object.keys(asignaturas).sort((a,b)=>a.localeCompare(b));
// console.log(nombres);

// Object.entries(asignaturas).forEach(
//     (item)=>{
//         console.log("Nombre "+item[0]+" horas "+item[1]);
//     }
// )

// Object.entries(asignaturas)
//       .sort(([nombreA,horasA],[nombreB,horasB])=>horasA-horasB)  
//       .forEach(([nombre,horas])=>console.log("Nombre "+nombre+" horas "+horas))

// let ordenMayorMenor=Object.entries(asignaturas)
//                    .sort(([nombreA,horasA],[nombreB,horasB])=>horasB-horasA);
// let asignaturaMasHoras=ordenMayorMenor[0][0];
// console.log(asignaturaMasHoras);  

// let asignaturasPlus={};
// for(let nombre in asignaturas){
//     asignaturasPlus[nombre]=asignaturas[nombre]+asignaturas[nombre]*0.1;
// }
// console.log(asignaturasPlus);

// let segundo_curso=[["Gimnasia",15],["Biologia",70],["Música",30]];
// console.log(Object.fromEntries(segundo_curso));















// {
//     "UK":124324
//     "USA":4325345
//     "ESP":5435
// }













// let suma=0;
// productos.forEach(
//     (articulo)=>{
//         // console.log(articulo["nombre"]);
//         // suma+=articulo["precio"];
//         let {precio,nombre}=articulo;
//         console.log(nombre);
//         suma+=precio;
//     }
// );
// console.log(suma);


// console.log(productos.filter(
//                 (articulo)=>articulo["categoria"]==="electronica"
//            ));
// let suma=0;
// productos.filter(
//             (articulo)=>articulo["categoria"]==="electronica"
//          ).forEach((articulo)=>{
//                 let {precio,nombre}=articulo;
//                 console.log(nombre);
//                 suma+=precio;
//         });
// console.log(suma);

// productos.sort(
//     (b,a)=>a["precio"]-b["precio"]
// );

//>0 hay que cambiar su orden relativo
//0
//<0 lo dejamos igual
// console.log(productos);
